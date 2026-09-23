<?php

namespace App\Concerns;

use App\Actions\CreateAction;
use App\Actions\DeleteAction;
use App\Actions\UpdateAction;
use App\Http\Requests\GeneralRequest;
use Illuminate\Http\Request;
use Plugins\Notes;

trait ControllerTrait
{
    use PayloadTrait;

    public $model;

    public function index(GeneralRequest $request)
    {
        return redirect()->action([self::class, 'getTable']);
    }

    public function getShow(GeneralRequest $request, $id)
    {
        try {
            $data = $this->model->findOrFail($id);
        } catch (\Throwable $th) {
            return $this->response($this->payload(TOAST_FAILED, $th->getMessage()));
        }

        // API pakai envelope Notes; web tetap dapat payload array seperti semula.
        if ($this->isApi()) {
            return Notes::single($data);
        }

        return $this->payload(TOAST_SUCCESS, $data);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);

        return $this->views($this->template(), [
            'model' => $data,
        ]);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $response = UpdateAction::run($request, $id, $this->model);

        return $this->response($response, null, 'update');
    }

    public function getDelete(GeneralRequest $request, $id)
    {
        $response = (new DeleteAction)->remove($id, $this->model);

        return $this->response($response, null, 'delete');
    }

    public function getTable(GeneralRequest $request)
    {
        $data = $this->getData()->cursorPaginate($request->input('per_page', 25))->withQueryString();

        return $this->views($this->template(), [
            'data' => $data,
            'fields' => $this->getFields(),
        ]);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template());
    }

    public function postCreate(GeneralRequest $request)
    {
        $response = CreateAction::run($request, $this->model);

        return $this->response($response, null, 'create');
    }

    public function postDelete(GeneralRequest $request)
    {
        $count = DeleteAction::run($request, $this->model);

        return $this->response($count, null, 'delete');
    }

    // END FUNCTION CONTROLLER

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
        ];

        return array_merge($default, $data);
    }

    protected function getFields()
    {
        // Build fields for filter from model's $filterColumns
        $fields = [];
        if (property_exists($this->model, 'filterColumns') && ! empty($this->model::$filterColumns)) {
            foreach ($this->model::$filterColumns as $key => $value) {
                if ($value === false || $value === null || $value === '') {
                    continue;
                }

                if (is_numeric($key)) {
                    $fields[$value] = ucwords(str_replace('_', ' ', $value));
                } else {
                    $fields[$key] = $value;
                }
            }
        }

        return $fields;
    }

    protected function views(string $view, array $data = [], int $status = 200)
    {
        if (request()->expectsJson()) {
            return Notes::data($data);
        }

        return view($view, $this->share($data));
    }

    /**
     * Export Excel global via blade (tanpa paket tambahan).
     *
     * Dipakai modul report (Hilang, Ganti Chip, dst.) — cukup sediakan
     * view `pages.{module}.excel` dan override `excelPayload()` bila
     * datanya bukan tabel standar. Modul CRUD tanpa view excel
     * otomatis 403 (tidak ada policy `exportExcel`) / 404.
     */
    public function getExportExcel(GeneralRequest $request)
    {
        set_time_limit(0);

        $view = $this->template('excel');
        abort_unless(view()->exists($view), 404, 'Excel view belum tersedia untuk modul ini.');

        [$filename, $viewData] = $this->excelPayload($request);

        return response()->view($view, array_merge($this->share(), $viewData), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Hook: [nama_file, data_view]. Default = data tabel standar.
     *
     * @return array{0: string, 1: array}
     */
    protected function excelPayload(Request $request): array
    {
        $filename = $this->template(true).'-'.now()->format('Ymd-His').'.xls';

        return [$filename, ['data' => $this->getData()->get()]];
    }

    protected function template($file = null, $folder = null, $core = false)
    {
        // Get the class name (e.g., UserController)
        $className = class_basename(get_class($this));

        // Remove 'Controller' suffix and convert to lowercase
        $module = strtolower(str_replace('Controller', '', $className));

        // Get the method name (e.g., getCreate)
        $method = debug_backtrace()[1]['function'];

        // Remove 'get' or 'post' prefix and convert to lowercase
        $action = strtolower(preg_replace('/^(get|post)/', '', $method));

        if (in_array($action, ['update', 'create'])) {
            $action = 'form';
        }

        if ($file) {
            $action = $file;
        }

        if ($file === true) {
            return $module;
        }

        if ($folder) {
            $module = $folder;
        }

        $path = 'pages.';

        if ($core) {
            $path = 'core.';
        }

        return $path.$module.'.'.$action;
    }

    protected function isApi(): bool
    {
        if (request()->hasHeader('authorization')) {
            return true;
        }

        if (request()->wantsJson()) {
            return true;
        }

        return request()->expectsJson() || request()->is('api/*');
    }

    protected function getData()
    {
        $query = $this->model->query();
        $request = request();

        // Filters: filters[field][operator] = value
        $filters = $request->input('filters', []);
        $availableColumns = $this->model::$filterColumns ?? [];
        $columnKeys = array_keys($availableColumns);
        // Normalize associative columns to plain list
        $allowedFields = array_map(fn ($k, $v) => is_numeric($k) ? $v : $k, $columnKeys, $availableColumns);

        foreach ($filters as $field => $conditions) {
            // Whitelist: skip fields not in $filterColumns
            if (! empty($allowedFields) && ! in_array($field, $allowedFields)) {
                continue;
            }
            // Handle leftJoin if field contains dot (relation.column)
            if (str_contains($field, '.')) {
                $parts = explode('.', $field);
                $relation = $parts[0];
                $column = $parts[1];
                if (method_exists($this->model, $relation)) {
                    $rel = $this->model->$relation();
                    $relatedTable = $rel->getRelated()->getTable();
                    $foreignKey = $rel->getQualifiedForeignKeyName();
                    $ownerKey = $rel->getQualifiedOwnerKeyName();
                    $query->leftJoin($relatedTable, $foreignKey, '=', $ownerKey);
                    $field = $relatedTable.'.'.$column;
                }
            } elseif (! str_contains($field, '.')) {
                $field = $this->model->getTable().'.'.$field;
            }

            if (is_array($conditions)) {
                foreach ($conditions as $operator => $value) {
                    if ($value === '' || $value === null) {
                        continue;
                    }
                    match ($operator) {
                        '$contains' => $query->whereRaw('LOWER('.$field.') LIKE ?', ['%'.strtolower($value).'%']),
                        '$eq' => $query->whereRaw('LOWER('.$field.') = ?', [strtolower($value)]),
                        '$gt' => $query->where($field, '>', $value),
                        '$gte' => $query->where($field, '>=', $value),
                        '$lt' => $query->where($field, '<', $value),
                        '$lte' => $query->where($field, '<=', $value),
                        '$ne' => $query->whereRaw('LOWER('.$field.') != ?', [strtolower($value)]),
                        '$in' => $query->whereIn($field, (array) $value),
                        default => $query->whereRaw('LOWER('.$field.') LIKE ?', ['%'.strtolower($value).'%']),
                    };
                }
            } elseif (is_string($conditions) && $conditions !== '') {
                $query->whereRaw('LOWER('.$field.') LIKE ?', ['%'.strtolower($conditions).'%']);
            }
        }

        // Legacy _q + _field search
        $q = $request->input('_q');
        $searchField = $request->input('_field');
        if ($q && $searchField && in_array($searchField, $allowedFields)) {
            $query->whereRaw('LOWER('.$searchField.') LIKE ?', ['%'.strtolower($q).'%']);
        }

        // Sort: sort[0] = column:direction
        $sort = $request->input('sort.0');
        $sortColumns = $this->model::$sortColumns ?? [];
        if ($sort) {
            $parts = explode(':', $sort);
            $col = $parts[0] ?? null;
            $dir = ($parts[1] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            if ($col && (empty($sortColumns) || in_array($col, $sortColumns))) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    /**
     * @param  string|null  $operation  'create' | 'update' | 'delete' | 'single'
     *                                  → menentukan `name` di envelope Notes.
     */
    protected function response(array $response, $redirect = null, ?string $operation = null)
    {
        if ($this->isApi()) {
            return $this->apiResponse($response, $operation);
        }

        if ($response['status']) {
            flash()->success($response['message']);
        } else {
            flash()->error($response['data']);
        }

        if ($redirect) {
            return $redirect;
        }

        return redirect()->back();
    }

    /**
     * Petakan payload internal {code, status, message, data} dari Actions
     * ke envelope standar Plugins\Notes.
     */
    protected function apiResponse(array $response, ?string $operation = null)
    {
        if (! ($response['status'] ?? false)) {
            // Payload gagal menyimpan pesan aslinya di `data` (mis. pesan SQL).
            // Di Notes, `data` dikosongkan saat error, jadi pesannya dipindah ke `message`.
            $data = $response['data'] ?? null;
            $message = is_string($data) && $data !== '' ? $data : ($response['message'] ?? null);

            return Notes::failed($response['code'] ?? 400, $message, $data);
        }

        $data = $response['data'] ?? null;

        return match ($operation) {
            'create' => Notes::create($data),
            'update' => Notes::update($data),
            'delete' => Notes::delete($data),
            'single' => Notes::single($data),
            default => Notes::data($data),
        };
    }

    protected function respondView(string $view, array $data = [])
    {
        if ($this->isApi()) {
            return Notes::single($data['model'] ?? $data[array_key_first($data)] ?? $data);
        }

        return view($view, $data);
    }
}

@forelse($data as $table)
    <tr>
        <td>{{ $table->field_name }}</td>
        <td>{{ $table->field_username }}</td>
        <td>{{ $table->field_role_name }}</td>
        <td>{{ $table->field_phone }}</td>
    </tr>
@empty
@endforelse
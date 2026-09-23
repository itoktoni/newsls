<?php

use Illuminate\Support\Facades\Schema;

it('creates opname tables in test_bka', function () {
    expect(Schema::hasTable('opname'))->toBeTrue();
    expect(Schema::hasTable('opname_detail'))->toBeTrue();
    expect(Schema::hasColumn('opname', 'opname_id'))->toBeTrue();
    expect(Schema::hasColumn('opname_detail', 'opname_detail_rfid'))->toBeTrue();
});

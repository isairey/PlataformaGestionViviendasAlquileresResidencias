<?php

use function PHPUnit\Framework\assertMatchesRegularExpression;

test('generate_code returns a string in the correct format', function () {
    $code = generate_code();

    expect($code)->toBeString();
    assertMatchesRegularExpression('/^[A-Z0-9]{3}-[A-Z0-9]{3}$/', $code);
});

test('generate_code produces unique values in a batch', function () {
    $codes = collect(range(1, 50))->map(fn () => generate_code());

    expect($codes->duplicates())->toBeEmpty();
});

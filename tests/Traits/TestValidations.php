<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Lang;

trait TestValidations
{
    protected abstract function routeStore();
    protected abstract function routeUpdate();
    
    protected function assertInvalidationInStoreAction (
        array $data,
        string $rule,
        $ruleParams = []
    )
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction (
        array $data,
        string $rule,
        $ruleParams = []
    )
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fileds,
        string $rule,
        array $ruleParams = []
    )
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fileds);

        foreach($fileds as $field)
        {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                Lang::get("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
            ]);
        }
    }
}

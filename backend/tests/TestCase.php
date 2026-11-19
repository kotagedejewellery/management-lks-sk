<?php

namespace Tests;

use Closure;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    /**
     * Exercise request validation with the production password policy without
     * making an external Have I Been Pwned request.
     */
    protected function withProductionPasswordPolicy(Closure $callback): mixed
    {
        $environment = app()->environment();
        $verifier = app(UncompromisedVerifier::class);
        app()->detectEnvironment(fn (): string => 'production');
        app()->instance(UncompromisedVerifier::class, new class implements UncompromisedVerifier {
            public function verify($data): bool
            {
                return true;
            }
        });

        try {
            return $callback();
        } finally {
            app()->instance(UncompromisedVerifier::class, $verifier);
            app()->detectEnvironment(fn (): string => $environment);
        }
    }
}

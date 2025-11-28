<?php

if (!function_exists('tenancy')) {
    function tenancy(): \Stancl\Tenancy\Tenancy
    {
        return app(\Stancl\Tenancy\Tenancy::class);
    }
}

if (!function_exists('central_connection')) {
    /**
     * Resolve the configured central (landlord) database connection name.
     */
    function central_connection(): string
    {
        return config(
            'tenancy.database.central_connection',
            config('database.central_connection', config('database.default'))
        );
    }
}

if (!function_exists('tenant')) {
    /**
     * Retrieve the currently initialized tenant or a specific attribute.
     */
    function tenant(?string $key = null)
    {
        $binding = null;

        if (app()->bound(\Stancl\Tenancy\Contracts\Tenant::class)) {
            $binding = app(\Stancl\Tenancy\Contracts\Tenant::class);
        } elseif (app()->bound('currentTenant')) {
            $binding = app('currentTenant');
        } elseif (app()->bound('currentSchool')) {
            $binding = app('currentSchool');
        }

        if (! $binding) {
            return null;
        }

        if ($key === null) {
            return $binding;
        }

        $value = data_get($binding, $key);

        if ($value !== null) {
            return $value;
        }

        $data = method_exists($binding, 'getAttribute')
            ? $binding->getAttribute('data')
            : null;

        return is_array($data) ? data_get($data, $key) : null;
    }
}

if (!function_exists('tenant_table_exists')) {
    /**
     * Determine if a table exists on the current tenant connection.
     */
    function tenant_table_exists(string $table, ?string $connection = null): bool
    {
        $connection = $connection
            ?? (app()->bound('currentSchool') || config('database.default') === 'tenant'
                ? 'tenant'
                : config('database.default'));

        try {
            $db = \Illuminate\Support\Facades\DB::connection($connection);
            $database = $db->getDatabaseName();

            if (! $database) {
                return false;
            }

            $result = $db->selectOne(
                'SELECT COUNT(*) AS table_count FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1',
                [$database, $table]
            );

            if ($result === null) {
                return false;
            }

            if (is_object($result)) {
                $count = (int) ($result->table_count ?? ($result->TABLE_COUNT ?? 0));
            } elseif (is_array($result)) {
                $count = (int) (array_values($result)[0] ?? 0);
            } else {
                $count = (int) $result;
            }

            return $count > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

        if (!function_exists('tenant_column_exists')) {
            /**
             * Determine if a column exists on the current tenant connection.
             */
            function tenant_column_exists(string $table, string $column, ?string $connection = null): bool
            {
                $connection = $connection
                    ?? (app()->bound('currentSchool') || config('database.default') === 'tenant'
                        ? 'tenant'
                        : config('database.default'));

                try {
                    $db = \Illuminate\Support\Facades\DB::connection($connection);
                    $database = $db->getDatabaseName();

                    if (! $database) {
                        return false;
                    }

                    $result = $db->selectOne(
                        'SELECT COUNT(*) AS column_count FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ? LIMIT 1',
                        [$database, $table, $column]
                    );

                    if ($result === null) {
                        return false;
                    }

                    if (is_object($result)) {
                        $count = (int) ($result->column_count ?? ($result->COLUMN_COUNT ?? 0));
                    } elseif (is_array($result)) {
                        $count = (int) (array_values($result)[0] ?? 0);
                    } else {
                        $count = (int) $result;
                    }

                    return $count > 0;
                } catch (\Throwable $e) {
                    return false;
                }
            }
        }
}

if (!function_exists('setting')) {
    /**
     * Get or set application settings.
     *
     * @param  string|array|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function setting(string|array|null $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return app(App\Models\Setting::class);
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                App\Models\Setting::set($k, $v);
            }

            return null;
        }

        return App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('perPage')) {
    /**
     * Get the pagination limit from tenant settings.
     *
     * @param  int  $default  Default number of items per page
     * @return int
     */
    function perPage(int $default = 15): int
    {
        $limit = (int) setting('pagination_limit', $default);

        // Ensure the limit is within allowed values
        $allowedLimits = [10, 15, 25, 50, 100];

        if (!in_array($limit, $allowedLimits)) {
            return $default;
        }

        return $limit;
    }
}

if (!function_exists('maxFileUpload')) {
    /**
     * Get the maximum file upload size from tenant settings (in kilobytes).
     *
     * Use in validation rules like: 'file' => 'required|file|max:' . maxFileUpload()
     *
     * @param  int  $default  Default size in megabytes
     * @return int  Size in kilobytes
     */
    function maxFileUpload(int $default = 10): int
    {
        $maxMB = (int) setting('max_file_upload', $default);

        // Ensure the limit is between 1 and 256 MB
        if ($maxMB < 1 || $maxMB > 256) {
            $maxMB = $default;
        }

        // Convert MB to KB for Laravel validation
        return $maxMB * 1024;
    }
}

if (!function_exists('maxFileUploadMB')) {
    /**
     * Get the maximum file upload size from tenant settings (in megabytes).
     *
     * Use for display purposes.
     *
     * @param  int  $default  Default size in megabytes
     * @return int  Size in megabytes
     */
    function maxFileUploadMB(int $default = 10): int
    {
        $maxMB = (int) setting('max_file_upload', $default);

        // Ensure the limit is between 1 and 256 MB
        if ($maxMB < 1 || $maxMB > 256) {
            return $default;
        }

        return $maxMB;
    }
}

if (!function_exists('currentCurrency')) {
    /**
     * Get the current default currency for the tenant.
     *
     * @return \App\Models\Currency|null
     */
    function currentCurrency()
    {
        return App\Models\Currency::getDefault();
    }
}

if (!function_exists('formatMoney')) {
    /**
     * Format an amount with the current currency symbol.
     *
     * @param  float  $amount
     * @param  \App\Models\Currency|null  $currency
     * @return string
     */
    function formatMoney(float $amount, $currency = null): string
    {
        if (!$currency) {
            $currency = currentCurrency();
        }

        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        return $currency->format($amount);
    }
}

if (!function_exists('format_money')) {
    /**
     * Backwards-compatible alias for formatMoney helper.
     */
    function format_money(float $amount, $currency = null): string
    {
        return formatMoney($amount, $currency);
    }
}

if (!function_exists('convertCurrency')) {
    /**
     * Convert an amount from one currency to another.
     *
     * @param  float  $amount
     * @param  string  $fromCode  Currency code to convert from
     * @param  string  $toCode    Currency code to convert to
     * @return float
     */
    function convertCurrency(float $amount, string $fromCode, string $toCode): float
    {
        $fromCurrency = App\Models\Currency::where('code', $fromCode)->first();
        $toCurrency = App\Models\Currency::where('code', $toCode)->first();

        if (!$fromCurrency || !$toCurrency) {
            return $amount; // Return original amount if currencies not found
        }

        return $fromCurrency->convertTo($amount, $toCurrency);
    }
}

if (!function_exists('bankPaymentInstructions')) {
    /**
     * Get bank payment instructions for displaying to users.
     *
     * Returns bank details configured in Payment Settings if the bank_transfer
     * gateway is enabled, otherwise returns null.
     *
     * @return array|null  Array with bank details or null if not configured
     */
    function bankPaymentInstructions(): ?array
    {
        $setting = App\Models\PaymentGatewaySetting::where('gateway', 'bank_transfer')
            ->where('is_enabled', true)
            ->first();

        if (!$setting || empty($setting->config)) {
            return null;
        }

        return $setting->config;
    }
}

if (!function_exists('curriculum_classes')) {
    /**
     * Get all active classes for the current school.
     *
     * @return \Illuminate\Support\Collection
     */
    function curriculum_classes(): \Illuminate\Support\Collection
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school ?? null;

        if (!$school) {
            return collect([]);
        }

        return App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name');
    }
}

if (!function_exists('get_school_classes')) {
    /**
     * Get all active classes for the current school with full model data.
     *
     * @param  bool  $withRelations  Include relationships (educationLevel, streams)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_school_classes(bool $withRelations = false): \Illuminate\Database\Eloquent\Collection
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school ?? null;

        if (!$school) {
            return collect([]);
        }

        $query = App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name');

        if ($withRelations) {
            $query->with(['educationLevel', 'streams']);
        }

        return $query->get();
    }
}

if (!function_exists('get_class_by_id')) {
    /**
     * Get a class by ID for the current school.
     *
     * @param  int  $classId
     * @param  bool  $withRelations
     * @return \App\Models\Academic\ClassRoom|null
     */
    function get_class_by_id(int $classId, bool $withRelations = false): ?\App\Models\Academic\ClassRoom
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school ?? null;

        if (!$school) {
            return null;
        }

        $query = App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('id', $classId);

        if ($withRelations) {
            $query->with(['educationLevel', 'streams', 'students', 'subjects']);
        }

        return $query->first();
    }
}

if (!function_exists('get_education_levels')) {
    /**
     * Get all active education levels for the current school.
     *
     * @param  bool  $withClasses  Include related classes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_education_levels(bool $withClasses = false): \Illuminate\Database\Eloquent\Collection
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school ?? null;

        if (!$school) {
            return collect([]);
        }

        $query = App\Models\Academic\EducationLevel::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($withClasses) {
            $query->with('classes');
        }

        return $query->get();
    }
}

if (!function_exists('get_classes_by_education_level')) {
    /**
     * Get all active classes for a specific education level.
     *
     * @param  int  $educationLevelId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_classes_by_education_level(int $educationLevelId): \Illuminate\Database\Eloquent\Collection
    {
        $school = request()->attributes->get('currentSchool') ?? auth()->user()->school ?? null;

        if (!$school) {
            return collect([]);
        }

        return App\Models\Academic\ClassRoom::where('school_id', $school->id)
            ->where('education_level_id', $educationLevelId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}

if (!function_exists('get_class_streams')) {
    /**
     * Get all streams for a specific class.
     *
     * @param  int  $classId
     * @param  bool  $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_class_streams(int $classId, bool $activeOnly = true): \Illuminate\Database\Eloquent\Collection
    {
        $query = App\Models\Academic\ClassStream::where('class_id', $classId);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('name')->get();
    }
}

if (!function_exists('get_class_capacity_info')) {
    /**
     * Get capacity information for a class.
     *
     * @param  int  $classId
     * @return array  ['capacity', 'enrolled', 'available', 'percentage', 'status']
     */
    function get_class_capacity_info(int $classId): array
    {
        $class = get_class_by_id($classId);

        if (!$class || !$class->capacity) {
            return [
                'capacity' => 0,
                'enrolled' => 0,
                'available' => 0,
                'percentage' => 0,
                'status' => 'unknown',
            ];
        }

        $enrolled = $class->active_students_count ?? 0;
        $capacity = $class->capacity;
        $available = max(0, $capacity - $enrolled);
        $percentage = $capacity > 0 ? ($enrolled / $capacity) * 100 : 0;

        // Determine status
        $status = 'available';
        if ($percentage >= 100) {
            $status = 'full';
        } elseif ($percentage >= 90) {
            $status = 'almost_full';
        } elseif ($percentage >= 70) {
            $status = 'filling_up';
        }

        return [
            'capacity' => $capacity,
            'enrolled' => $enrolled,
            'available' => $available,
            'percentage' => round($percentage, 1),
            'status' => $status,
        ];
    }
}

if (!function_exists('get_class_subjects')) {
    /**
     * Get all subjects assigned to a class.
     *
     * @param  int  $classId
     * @param  bool  $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_class_subjects(int $classId, bool $activeOnly = true): \Illuminate\Database\Eloquent\Collection
    {
        $class = get_class_by_id($classId);

        if (!$class) {
            return collect([]);
        }

        $query = $class->subjects();

        if ($activeOnly) {
            $query->where('subjects.is_active', true);
        }

        return $query->orderBy('subjects.name')->get();
    }
}

if (!function_exists('class_has_capacity')) {
    /**
     * Check if a class has available capacity for new students.
     *
     * @param  int  $classId
     * @param  int  $requiredSlots
     * @return bool
     */
    function class_has_capacity(int $classId, int $requiredSlots = 1): bool
    {
        $info = get_class_capacity_info($classId);
        return $info['available'] >= $requiredSlots;
    }
}

if (!function_exists('format_class_name')) {
    /**
     * Format class name with optional education level and stream.
     *
     * @param  \App\Models\Academic\ClassRoom  $class
     * @param  bool  $includeEducationLevel
     * @param  string|null  $streamName
     * @return string
     */
    function format_class_name(\App\Models\Academic\ClassRoom $class, bool $includeEducationLevel = false, ?string $streamName = null): string
    {
        $name = $class->name;

        if ($includeEducationLevel && $class->educationLevel) {
            $name = $class->educationLevel->name . ' - ' . $name;
        }

        if ($streamName) {
            $name .= ' ' . $streamName;
        }

        return $name;
    }
}

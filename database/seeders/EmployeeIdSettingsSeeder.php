<?php

namespace Database\Seeders;

use App\Models\EmployeeIdSetting;
use Illuminate\Database\Seeder;

class EmployeeIdSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (EmployeeIdSetting::count() > 0) {
            return;
        }

        $templates = [
            [
                'template_name' => 'Classic Royal Blue',
                'card_width' => '85.6',
                'card_height' => '54',
                'background_color' => '#0d47a1',
                'text_color' => '#ffffff',
                'header_text' => 'Official Staff Identification',
                'header_color' => '#0a2e6d',
                'fields_to_display' => ['full_name', 'position', 'employee_number', 'department', 'phone'],
                'include_qr_code' => true,
                'qr_code_position' => 'bottom-right',
                'qr_code_size' => '80',
                'include_photo' => true,
                'photo_position' => 'top-left',
                'photo_size' => '110',
                'font_family' => 'Poppins, sans-serif',
                'font_size' => '13',
                'layout_settings' => [
                    'border_radius' => 14,
                    'border_color' => '#0a2e6d',
                    'border_width' => 2,
                    'header_font_size' => 18,
                    'header_font_weight' => 700,
                    'field_spacing' => 8,
                ],
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'template_name' => 'Minimal Slate',
                'card_width' => '85.6',
                'card_height' => '54',
                'background_color' => '#ffffff',
                'text_color' => '#1f2937',
                'header_text' => 'Employee Identification Card',
                'header_color' => '#1f2937',
                'fields_to_display' => ['full_name', 'position', 'employee_type', 'department'],
                'include_qr_code' => true,
                'qr_code_position' => 'bottom-left',
                'qr_code_size' => '70',
                'include_photo' => true,
                'photo_position' => 'top-right',
                'photo_size' => '100',
                'font_family' => 'Inter, sans-serif',
                'font_size' => '12',
                'layout_settings' => [
                    'border_radius' => 10,
                    'border_color' => '#e5e7eb',
                    'border_width' => 1,
                    'header_font_size' => 16,
                    'field_spacing' => 6,
                ],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'template_name' => 'Vertical Night Badge',
                'card_width' => '54',
                'card_height' => '85.6',
                'background_color' => '#111827',
                'text_color' => '#f3f4f6',
                'header_text' => 'Staff Access Pass',
                'header_color' => '#60a5fa',
                'fields_to_display' => ['full_name', 'position', 'employee_number', 'hire_date'],
                'include_qr_code' => true,
                'qr_code_position' => 'top-right',
                'qr_code_size' => '60',
                'include_photo' => true,
                'photo_position' => 'center',
                'photo_size' => '130',
                'font_family' => 'Montserrat, sans-serif',
                'font_size' => '12',
                'layout_settings' => [
                    'border_radius' => 18,
                    'border_color' => '#1f2937',
                    'border_width' => 0,
                    'header_font_size' => 17,
                    'field_spacing' => 7,
                ],
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'template_name' => 'Sunset Gradient',
                'card_width' => '85.6',
                'card_height' => '54',
                'background_color' => '#f97316',
                'text_color' => '#fff7ed',
                'header_text' => 'Employee Pass',
                'header_color' => '#fcd34d',
                'fields_to_display' => ['full_name', 'position', 'department', 'email'],
                'include_qr_code' => true,
                'qr_code_position' => 'bottom-right',
                'qr_code_size' => '75',
                'include_photo' => true,
                'photo_position' => 'top-left',
                'photo_size' => '105',
                'font_family' => 'Nunito, sans-serif',
                'font_size' => '13',
                'layout_settings' => [
                    'border_radius' => 16,
                    'border_color' => '#fb923c',
                    'border_width' => 2,
                    'header_font_size' => 17,
                    'field_spacing' => 7,
                ],
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmployeeIdSetting::create($template);
        }
    }
}

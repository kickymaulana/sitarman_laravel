<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan data riil dari berkas customer.sql tanpa gangguan tag sitasi
        $customers = [
            ['id' => 1, 'customer' => 'MERCATOR', 'model' => 'MD 150 380 M', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (ECO 2.0)', 'size' => 'M'],
            ['id' => 2, 'customer' => 'MERCATOR', 'model' => 'MD 150 380 S', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (ECO 2.0)', 'size' => 'S'],
            ['id' => 3, 'customer' => 'YTY INDUSTRY GROUP SDN BHD (YA001)', 'model' => 'MD 108 380 XXL', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'XXL YA001'],
            ['id' => 4, 'customer' => 'YTY INDUSTRY GROUP SDN BHD (YA001)', 'model' => 'MD 061 380 XL', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'XL YA001'],
            ['id' => 5, 'customer' => 'YTY INDUSTRY GROUP SDN BHD (YA024)', 'model' => 'MD Y61A 380 M', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'M YA024'],
            ['id' => 6, 'customer' => 'YTY INDUSTRY GROUP SDN BHD (YA023)', 'model' => 'MD Y61A 380 M', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'M YA023'],
            ['id' => 7, 'customer' => 'YTY INDUSTRY GROUP SDN BHD (YA022)', 'model' => 'MD Y61A 380 M', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'M YA022'],
            ['id' => 8, 'customer' => 'PMP (Z03C)', 'model' => 'MD 104 380 XL', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON', 'size' => 'XL Z03C'],
            ['id' => 9, 'customer' => 'PMP (Z02C)', 'model' => 'MD 104 380 XL', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON', 'size' => 'XL Z02C'],
            ['id' => 10, 'customer' => 'PMP (Z01C)', 'model' => 'MD 104 380 XL', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON', 'size' => 'XL Z01C'],
            ['id' => 11, 'customer' => 'PMP (Z02C)', 'model' => 'MD 68 380 XS', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON', 'size' => 'XS Z02C'],
            ['id' => 12, 'customer' => 'PMP (Z01C)', 'model' => 'MD 43 430 XS', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON', 'size' => 'XS Z01C'],
            ['id' => 13, 'customer' => 'PMP (Z02C)', 'model' => 'MD 124 380 L', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON/R1', 'size' => 'L Z02C'],
            ['id' => 14, 'customer' => 'PMP (Z01C)', 'model' => 'MD 124 380 L', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON/R1', 'size' => 'L Z01C'],
            ['id' => 15, 'customer' => 'PMP (Z02C)', 'model' => 'MD 124 380 M', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON/R1', 'size' => 'M Z02C'],
            ['id' => 16, 'customer' => 'PMP (Z01C)', 'model' => 'MD 124 380 M', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON/R1', 'size' => 'M Z01C'],
            ['id' => 35, 'customer' => 'SRITRANG GLOVES', 'model' => 'MD 36 368 XL-01', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (V CROTCH)', 'size' => 'XL-01'],
            ['id' => 41, 'customer' => 'KOSSAN', 'model' => 'MD 43 380 XL', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (JANFE38P / R2)', 'size' => 'XL'],
            ['id' => 47, 'customer' => 'ANSELL THAILAND (LTD)', 'model' => 'MD 133 360 M', 'spesifikasi' => 'SMOOTH FULL SPRAY ON', 'size' => 'M'],
            ['id' => 49, 'customer' => 'KOSSAN', 'model' => 'MD 43 400 L', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (JANFE40P / R1)', 'size' => 'L'],
            ['id' => 61, 'customer' => 'INTCO', 'model' => 'MD 124 380 L', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (ECO / R1A)', 'size' => 'L'],
            ['id' => 63, 'customer' => 'INTCO', 'model' => 'MD 124 380 S', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (ECO / R1A)', 'size' => 'S'],
            ['id' => 64, 'customer' => 'SRITRANG GLOVES', 'model' => 'MD 87 368 M-01', 'spesifikasi' => 'FINGER SPRAY FULL SPRAY ON (V CROTCH)', 'size' => 'M-01'],
            ['id' => 73, 'customer' => 'MAHSING', 'model' => 'MD 43 380 XL', 'spesifikasi' => 'FINGER TEXTURE FULL SPRAY ON (MS1)', 'size' => 'XL'],
        ];

        foreach ($customers as $customer) {
            DB::table('customer')->updateOrInsert(
                ['id' => $customer['id']],
                [
                    'customer' => $customer['customer'],
                    'model' => $customer['model'],
                    'spesifikasi' => $customer['spesifikasi'],
                    'size' => $customer['size'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

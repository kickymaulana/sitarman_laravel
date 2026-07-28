<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { IconArrowLeft, IconPrinter, IconFlame } from "@tabler/icons-vue";

const props = defineProps<{
    records: Array<{
        id: number;
        posisi_former: number;
        hasil_test_180: string;
        hasil_180: number;
        hasil_test_200: string;
        hasil_200: number;
        sampel: string;
        berat_former: number;
        kode_bakar: number;
        kode_tanah: string;
        tanggal_keluar_oven: string;
        customer: {
            customer: string;
            model: string;
            size: string;
        } | null;
        thermal_pintu: {
            thermal_pintu: string;
        } | null;
        oven: {
            oven: string;
        } | null;
        jam_keluar_oven: {
            jam_keluar_oven: string;
        } | null;
    }>;
    tanggal: string;
}>();

import { computed } from 'vue'

const printStruk = () => {
    window.print();
};

const pct180 = computed(() => {
    const total = props.records.length
    if (!total) return 0
    const ok = props.records.filter(r => r.hasil_test_180 === 'OK').length
    return Math.round((ok / total) * 100)
})

const pct200 = computed(() => {
    const total = props.records.length
    if (!total) return 0
    const ok = props.records.filter(r => r.hasil_test_200 === 'OK').length
    return Math.round((ok / total) * 100)
})
</script>

<template>
    <Head title="Struk Ringkasan Thermal Shock" />

    <div class="min-h-screen bg-zinc-100 dark:bg-zinc-950 p-4 flex flex-col items-center justify-start gap-4">
        <!-- Tombol Aksi Navigasi (Otomatis Sembunyi Saat Di-print) -->
        <div class="w-full max-w-lg flex items-center justify-between print:hidden">
            <Button as-child variant="outline" size="sm" class="h-9">
                <Link :href="route('thermalshock.index')">
                    <IconArrowLeft class="mr-1.5 size-4" /> Kembali
                </Link>
            </Button>
            <Button @click="printStruk" variant="default" size="sm" class="h-9 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">
                <IconPrinter class="mr-1.5 size-4" /> Cetak / Save PDF
            </Button>
        </div>

        <!-- Tampilan Struk Pembayaran / Nota Ringkas -->
        <div class="w-full max-w-lg bg-white text-zinc-900 rounded-2xl shadow-xl border border-zinc-200 p-6 print:shadow-none print:border-none print:w-full print:max-w-none">

            <!-- Header Struk -->
            <div class="text-center pb-4 border-b border-dashed border-zinc-300">
                <div class="flex items-center justify-center gap-1.5 font-bold text-lg text-zinc-900">
                    <IconFlame class="size-5 text-amber-600" />
                    <span>RINGKASAN THERMAL SHOCK</span>
                </div>
                <p class="text-xs text-zinc-500 mt-1">{{ props.tanggal }}</p>
                <p v-if="props.records[0]?.thermal_pintu" class="text-xs font-semibold text-zinc-700 mt-0.5">
                    {{ props.records[0].thermal_pintu.thermal_pintu }}
                </p>
            </div>

            <!-- Tabel -->
            <div class="py-4">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                            <th class="pb-2">Customer / Model</th>
                            <th class="pb-2 text-center">Kode</th>
                            <th class="pb-2 text-center">Oven</th>
                            <th class="pb-2 text-center">Tgl Oven</th>
                            <th class="pb-2 text-center">Jam</th>
                            <th class="pb-2 text-center">Sampel</th>
                            <th class="pb-2 text-center">Berat</th>
                            <th class="pb-2 text-center">Hasil 180</th>
                            <th class="pb-2 text-center">Hasil 200</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs">
                        <tr v-for="item in props.records" :key="item.id" class="align-middle">
                            <td class="py-2 pr-2">
                                <div class="font-bold text-zinc-900 leading-snug">
                                    {{ item.customer?.customer ?? '-' }}
                                </div>
                                <div class="text-[11px] text-zinc-500">
                                    {{ item.customer?.model ?? '-' }} <span v-if="item.customer?.size">({{ item.customer.size }})</span>
                                </div>
                            </td>

                            <td class="py-2.5 text-center px-1 font-mono text-zinc-700 text-[11px]">
                                <div>{{ item.kode_bakar ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-400">{{ item.kode_tanah || '-' }}</div>
                            </td>

                            <td class="py-2.5 text-center px-1 text-zinc-700">
                                {{ item.oven?.oven ?? '-' }}
                            </td>

                            <td class="py-2.5 text-center px-1 font-mono text-[11px] text-zinc-600">
                                {{ item.tanggal_keluar_oven ?? '-' }}
                            </td>

                            <td class="py-2.5 text-center px-1 font-mono text-[11px] text-zinc-600">
                                {{ item.jam_keluar_oven?.jam_keluar_oven ?? '-' }}
                            </td>

                            <td class="py-2.5 text-center px-1 font-semibold text-zinc-800">
                                {{ item.sampel ?? '-' }}
                            </td>

                            <td class="py-2.5 text-center px-1 font-mono text-zinc-700">
                                {{ item.berat_former }}g
                            </td>

                            <td class="py-2.5 text-center px-1">
                                <div class="font-bold" :class="{
                                    'text-emerald-600': item.hasil_test_180 === 'OK',
                                    'text-rose-600': item.hasil_test_180 === 'NG',
                                    'text-zinc-400': item.hasil_test_180 === 'Belum Tes'
                                }">{{ item.hasil_test_180 }}</div>
                                <div v-if="item.hasil_180" class="text-[10px] text-zinc-500 font-mono">{{ item.hasil_180 }}°C</div>
                            </td>

                            <td class="py-2.5 text-center pl-1">
                                <div class="font-bold" :class="{
                                    'text-emerald-600': item.hasil_test_200 === 'OK',
                                    'text-rose-600': item.hasil_test_200 === 'NG' || item.hasil_test_200 === 'Pecah 180',
                                    'text-zinc-400': item.hasil_test_200 === 'Belum Tes'
                                }">{{ item.hasil_test_200 }}</div>
                                <div v-if="item.hasil_200" class="text-[10px] text-zinc-500 font-mono">{{ item.hasil_200 }}°C</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Struk -->
            <div class="pt-4 border-t border-dashed border-zinc-300 text-center text-[11px] text-zinc-500 space-y-2">
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="bg-emerald-50 rounded-lg p-2">
                        <div class="font-semibold text-zinc-600">Hasil 180</div>
                        <div class="text-lg font-black" :class="pct180 >= 80 ? 'text-emerald-600' : 'text-rose-600'">{{ pct180 }}%</div>
                        <div class="text-[10px] text-zinc-500">OK</div>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-2">
                        <div class="font-semibold text-zinc-600">Hasil 200</div>
                        <div class="text-lg font-black" :class="pct200 >= 80 ? 'text-emerald-600' : 'text-rose-600'">{{ pct200 }}%</div>
                        <div class="text-[10px] text-zinc-500">OK</div>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs font-semibold text-zinc-800 pt-1">
                    <span>Total Sample:</span>
                    <span>{{ props.records.length }} Item</span>
                </div>
                <p class="pt-1 italic">*** Terima Kasih ***</p>
            </div>

        </div>
    </div>
</template>

<style scoped>
@media print {
    body {
        background-color: white !important;
    }
}
</style>

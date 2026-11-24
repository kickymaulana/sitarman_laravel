<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { IconArrowLeft, IconPrinter } from "@tabler/icons-vue";

type User = { name: string };
type Sample = Record<string, unknown> & {
    id: number;
    tgl_test: string;
    sesi_ke: number;
    urutan: number;
    kode_sample: string;
    creator: User | null;
    completer: User | null;
    status: string;
};

const props = defineProps<{ records: Sample[]; tanggal: string }>();
const printStruk = () => window.print();
const display = (value: unknown) => value === null || value === undefined || value === "" ? "-" : String(value);
const columns = [
    { key: "sesi_ke", label: "Sesi" },
    { key: "urutan", label: "Urutan" },
    { key: "kode_sample", label: "Kode Sample" },
    { key: "density", label: "Density" },
    { key: "palm_water", label: "Palm Water (gr)" },
    { key: "mc_water", label: "MC Water (gr)" },
    { key: "sl_water", label: "SL Water (gr)" },
];
</script>

<template>
    <Head title="Struk Ringkasan Density Water Sample" />
    <main class="min-h-screen bg-zinc-100 p-4 text-zinc-900 print:bg-white print:p-0">
        <div class="mx-auto mb-4 flex max-w-2xl justify-between gap-2 print:hidden">
            <Button as-child variant="outline" size="sm">
                <Link :href="route('density-water-sample.index')"><IconArrowLeft class="mr-2 size-4" />Kembali</Link>
            </Button>
            <Button size="sm" @click="printStruk"><IconPrinter class="mr-2 size-4" />Cetak / Save PDF</Button>
        </div>
        <section class="mx-auto max-w-2xl rounded-xl border bg-white p-5 shadow-lg print:max-w-none print:rounded-none print:border-0 print:p-0 print:shadow-none">
            <header class="border-b border-dashed pb-4 text-center">
                <h1 class="text-lg font-bold">RINGKASAN DENSITY WATER SAMPLE</h1>
                <p class="mt-1 text-xs">Dicetak: {{ tanggal }}</p>
                <p class="text-xs">Total Sample: {{ records.length }}</p>
            </header>
            <table class="my-4 w-full table-fixed border-collapse text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-300">
                        <th v-for="column in columns" :key="column.key" class="px-1 py-2 text-[10px] font-bold">{{ column.label }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in props.records" :key="item.id" class="sample border-b border-zinc-200">
                        <td v-for="column in columns" :key="column.key" class="break-words px-1 py-2">{{ display(item[column.key]) }}</td>
                    </tr>
                </tbody>
            </table>
            <footer class="pt-4 text-center text-xs">Total Sample: {{ records.length }} Item</footer>
        </section>
    </main>
</template>

<style scoped>
@media print {
    .sample {
        break-inside: avoid;
    }
}
</style>

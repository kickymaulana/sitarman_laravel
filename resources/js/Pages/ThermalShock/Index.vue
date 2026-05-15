<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    IconPlus,
    IconPencil,
    IconSearch,
    IconX,
    IconFlask,
    IconEye,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: {
        data: Array<{
            id: number;
            hari_tgl: string;
            suhu_testing: number;
            details_count: number;
            created_at: string
        }>;
        links: any[];
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;

watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("thermalshock.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Data Thermal Shock" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlask class="size-6 text-primary" />
                    Laporan Test Thermal Shock
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari berdasarkan tanggal..."
                            class="pl-10 pr-10"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button
                        as-child
                        class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95"
                    >
                        <Link :href="route('thermalshock.create')">
                            <IconPlus class="mr-2 size-4" />
                            Entry Baru
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Hari / Tanggal</TableHead>
                                <TableHead class="text-center">Suhu Testing</TableHead>
                                <TableHead class="text-center">Jumlah Item</TableHead>
                                <TableHead class="hidden md:table-cell text-center">Waktu Input</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="thermalshocks.data.length === 0">
                                <TableCell
                                    colspan="5"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Belum ada data laporan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="item in thermalshocks.data"
                                :key="item.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <TableCell class="font-bold text-primary tracking-wide">
                                    {{ new Date(item.hari_tgl).toLocaleDateString("id-ID", {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    }) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-xs font-bold">
                                        {{ item.suhu_testing }}°C
                                    </span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span class="font-medium">{{ item.details_count }} Item</span>
                                </TableCell>
                                <TableCell
                                    class="hidden md:table-cell text-center text-muted-foreground text-sm"
                                >
                                    {{ new Date(item.created_at).toLocaleString("id-ID") }}
                                </TableCell>

                                <TableCell class="text-right flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="size-8 hover:text-primary"
                                        as-child
                                    >
                                        <Link :href="route('thermalshock.show', item.id)">
                                            <IconEye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 hover:text-primary"
                                        as-child
                                    >
                                        <Link :href="route('thermalshock.edit', item.id)">
                                            <IconPencil class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6"
                >
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ thermalshocks.from ?? 0 }} -
                        {{ thermalshocks.to ?? 0 }} dari {{ thermalshocks.total }} laporan
                    </p>

                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in thermalshocks.links" :key="k">
                            <Button
                                v-if="link.url === null"
                                variant="outline"
                                size="sm"
                                disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child
                                variant="outline"
                                size="sm"
                                class="text-xs px-3 h-8 transition-all"
                                :class="{
                                    'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm':
                                        link.active,
                                }"
                            >
                                <Link
                                    :href="link.url"
                                    v-html="cleanLabel(link.label)"
                                />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

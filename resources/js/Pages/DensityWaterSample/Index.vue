<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Head, Link, router } from "@inertiajs/vue3";
import { IconDroplet, IconPencil, IconPlus, IconSearch } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

type User = { id: number; name: string; username: string };
type Sample = {
    id: number;
    tgl_test: string;
    sesi_ke: number;
    urutan: number;
    kode_sample: string;
    no_open: string;
    density: string | null;
    status: string;
    creator: User;
    completer: User | null;
};

const props = defineProps<{
    samples: {
        data: Sample[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        from: number | null;
        to: number | null;
        total: number;
    };
    sessions: number[];
    filters: { tgl_test?: string; sesi_ke?: number | string; search?: string };
    canWrite: boolean;
}>();

const tglTest = ref(props.filters.tgl_test || "");
const sesiKe = ref(props.filters.sesi_ke ? String(props.filters.sesi_ke) : "");
const search = ref(props.filters.search || "");
let timeout: ReturnType<typeof setTimeout>;

watch([tglTest, sesiKe, search], () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route("density-water-sample.index"), {
            tgl_test: tglTest.value || undefined,
            sesi_ke: sesiKe.value || undefined,
            search: search.value || undefined,
        }, { preserveState: true, replace: true });
    }, 400);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Density Water Sample" />
    <div class="flex flex-col gap-4 p-4 pt-4 md:p-8">
        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <CardTitle class="flex items-center gap-2 text-xl font-bold">
                    <IconDroplet class="size-6 text-primary" />
                    Density Water Sample
                </CardTitle>
                <div v-if="canWrite" class="flex flex-wrap gap-2">
                    <Button as-child>
                        <Link :href="route('density-water-sample.create', { tgl_test: tglTest || undefined, sesi_ke: sesiKe || undefined })">
                            <IconPlus class="mr-2 size-4" /> Tambah Data
                        </Link>
                    </Button>
                    <Button v-if="tglTest" variant="outline" as-child>
                        <Link :href="route('density-water-sample.create', { tgl_test: tglTest, new_session: true })">
                            <IconPlus class="mr-2 size-4" /> Buat Sesi Baru
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="mb-6 grid gap-3 md:grid-cols-2">
                    <div>
                        <p class="mb-2 text-sm font-medium">Pilih Tanggal Test</p>
                        <Input v-model="tglTest" type="date" />
                    </div>
                    <div v-if="sessions.length > 0">
                        <p class="mb-2 text-sm font-medium">Pilih Sesi</p>
                        <select v-model="sesiKe" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                            <option value="">Semua sesi</option>
                            <option v-for="session in sessions" :key="session" :value="String(session)">Sesi {{ session }}</option>
                        </select>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium">Cari Setelah Memilih Tanggal</p>
                        <div class="relative">
                            <IconSearch class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input v-model="search" placeholder="Kode sample, no. open, atau urutan" class="pl-10" />
                        </div>
                    </div>
                </div>

                <div v-if="canWrite && tglTest && sesiKe && samples.total > 0" class="mb-6 flex justify-end">
                    <Button as-child variant="outline">
                        <Link :href="route('density-water-sample.bulk-complete', { tgl_test: tglTest, sesi_ke: sesiKe })">
                            Lengkapi Semua Data Sesi {{ sesiKe }}
                        </Link>
                    </Button>
                </div>

                <div class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Sesi</TableHead>
                                <TableHead>Urutan</TableHead>
                                <TableHead>Tanggal Test</TableHead>
                                <TableHead>Kode Sample</TableHead>
                                <TableHead>No. Open</TableHead>
                                <TableHead>Density</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Penginput</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="samples.data.length === 0">
                                <TableCell colspan="8" class="h-24 text-center text-muted-foreground">
                                    {{ tglTest ? "Data pada tanggal ini tidak ditemukan." : "Pilih tanggal test atau lihat seluruh data." }}
                                </TableCell>
                            </TableRow>
                            <TableRow v-for="item in samples.data" :key="item.id">
                                <TableCell>{{ item.sesi_ke }}</TableCell>
                                <TableCell class="font-bold">{{ item.urutan }}</TableCell>
                                <TableCell>{{ item.tgl_test }}</TableCell>
                                <TableCell>{{ item.kode_sample }}</TableCell>
                                <TableCell>{{ item.no_open }}</TableCell>
                                <TableCell>{{ item.density ?? "-" }}</TableCell>
                                <TableCell>
                                    <Badge :variant="item.status === 'selesai' ? 'default' : 'secondary'">
                                        {{ item.status === "selesai" ? "Selesai" : "Belum Lengkap" }}
                                    </Badge>
                                </TableCell>
                                <TableCell>{{ item.creator?.name || "-" }}</TableCell>
                                <TableCell class="text-right">
                                    <div v-if="canWrite" class="flex justify-end gap-2">
                                        <Button v-if="item.status !== 'selesai'" size="sm" as-child>
                                            <Link :href="route('density-water-sample.complete', item.id)">Lengkapi</Link>
                                        </Button>
                                        <Button variant="outline" size="icon" as-child>
                                            <Link :href="route('density-water-sample.edit', item.id)">
                                                <IconPencil class="size-4" />
                                            </Link>
                                        </Button>
                                    </div>
                                    <span v-else class="text-sm text-muted-foreground">Lihat saja</span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="mt-6 flex flex-col items-center justify-between gap-4 md:flex-row">
                    <p class="text-xs text-muted-foreground">
                        Menampilkan {{ samples.from ?? 0 }}–{{ samples.to ?? 0 }} dari {{ samples.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, index) in samples.links" :key="index">
                            <Button v-if="!link.url" variant="outline" size="sm" disabled v-html="cleanLabel(link.label)" />
                            <Button v-else as-child variant="outline" size="sm" :class="{ 'bg-primary text-primary-foreground': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconEye, IconTrash, IconDotsVertical, IconFlask, IconPencil, IconClock } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    chemical: {
        id: number;
        tgl_test: string;
        kode_alkali: string;
        alkali_jam_mulai: string;
        alkali_jam_selesai: string;
        kode_acid: string;
        acid_jam_mulai: string;
        acid_jam_selesai: string;
    };
}>();
</script>

<template>
    <Head title="Detail Pengujian Chemical" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('chemical.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Detail Log Pengujian</h2>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="outline" size="sm" class="shadow-sm">
                    <Link :href="route('produkchemical.index', props.chemical.id)">
                        <IconEye class="mr-2 size-4 text-primary" /> Lihat Produk
                    </Link>
                </Button>

                <AlertDialog>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon">
                                <IconDotsVertical class="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem class="cursor-pointer" as-child>
                                <Link :href="route('chemical.edit', props.chemical.id)">
                                    <IconPencil class="mr-2 size-4 text-muted-foreground" /> Edit Data
                                </Link>
                            </DropdownMenuItem>
                            <AlertDialogTrigger as-child>
                                <DropdownMenuItem class="text-destructive cursor-pointer">
                                    <IconTrash class="mr-2 size-4" /> Hapus Log
                                </DropdownMenuItem>
                            </AlertDialogTrigger>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                            <AlertDialogDescription>
                                Apakah Anda yakin ingin menghapus permanen log pengujian tanggal
                                <strong class="text-foreground">
                                    {{ new Date(props.chemical.tgl_test).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}
                                </strong>? Tindakan ini menghapus seluruh data terkait dan tidak dapat dibatalkan.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="router.delete(route('chemical.destroy', props.chemical.id))" class="bg-destructive text-white hover:bg-destructive/90">
                                Ya, Hapus
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <div class="max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6">

            <Card class="border-none shadow-md md:col-span-1">
                <CardHeader class="border-b bg-muted/10 pb-4">
                    <div class="flex items-center gap-2">
                        <IconFlask class="size-5 text-primary" />
                        <CardTitle class="text-lg">Informasi Utama</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 space-y-4">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">ID Record</span>
                        <p class="text-base font-mono font-medium mt-1">#{{ props.chemical.id }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal Log</span>
                        <p class="text-base font-medium mt-1">
                            {{ new Date(props.chemical.tgl_test).toLocaleDateString("id-ID", { weekday: "long", day: "2-digit", month: "long", year: "numeric" }) }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <Card class="border-none shadow-md bg-blue-50/30 dark:bg-blue-950/10 border-t-4 border-t-blue-500">
                    <CardHeader class="pb-3 border-b">
                        <CardTitle class="text-blue-600 text-md font-bold">Kondisi Alkali</CardTitle>
                    </CardHeader>
                    <CardContent class="p-5 space-y-4">
                        <div>
                            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Kode Alkali</span>
                            <span class="inline-flex items-center rounded-md bg-blue-100 dark:bg-blue-900/50 px-2.5 py-0.5 text-sm font-medium text-blue-800 dark:text-blue-300 mt-1">
                                {{ props.chemical.kode_alkali }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <div>
                                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider flex items-center gap-1"><IconClock class="size-3.5" /> Mulai</span>
                                <p class="text-sm font-semibold mt-0.5 text-zinc-700 dark:text-zinc-300">{{ props.chemical.alkali_jam_mulai }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider flex items-center gap-1 justify-end"><IconClock class="size-3.5" /> Selesai</span>
                                <p class="text-sm font-semibold mt-0.5 text-zinc-700 dark:text-zinc-300">{{ props.chemical.alkali_jam_selesai }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-md bg-purple-50/30 dark:bg-purple-950/10 border-t-4 border-t-purple-500">
                    <CardHeader class="pb-3 border-b">
                        <CardTitle class="text-purple-600 text-md font-bold">Kondisi Acid</CardTitle>
                    </CardHeader>
                    <CardContent class="p-5 space-y-4">
                        <div>
                            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block">Kode Acid</span>
                            <span class="inline-flex items-center rounded-md bg-purple-100 dark:bg-purple-900/50 px-2.5 py-0.5 text-sm font-medium text-purple-800 dark:text-purple-300 mt-1">
                                {{ props.chemical.kode_acid }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <div>
                                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider flex items-center gap-1"><IconClock class="size-3.5" /> Mulai</span>
                                <p class="text-sm font-semibold mt-0.5 text-zinc-700 dark:text-zinc-300">{{ props.chemical.acid_jam_mulai }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider flex items-center gap-1 justify-end"><IconClock class="size-3.5" /> Selesai</span>
                                <p class="text-sm font-semibold mt-0.5 text-zinc-700 dark:text-zinc-300">{{ props.chemical.acid_jam_selesai }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

        </div>
    </div>
</template>

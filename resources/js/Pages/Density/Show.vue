<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconEye, IconTrash, IconDotsVertical, IconHammer, IconPencil } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    density: {
        id: number;
        tgl: string;
        user: { name: string } | null;
    };
}>();
</script>

<template>
    <Head title="Detail Density" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">

        <!-- Header Aksi -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('density.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Detail Density</h2>
            </div>

            <div class="flex items-center gap-2">
                <!-- Arahkan ke rute produk_density Anda (contoh: produkdensity.index) -->
                <Button as-child variant="outline" size="sm" class="shadow-sm">
                    <Link :href="route('produkdensity.index', props.density.id)">
                        <IconEye class="mr-2 size-4 text-primary" /> Produk
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
                                <Link :href="route('density.edit', props.density.id)">
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
                                Apakah Anda yakin ingin menghapus permanen log density tanggal
                                <strong class="text-foreground">
                                    {{ props.density.tgl ? new Date(props.density.tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                                </strong>? Tindakan ini tidak dapat dibatalkan.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="router.delete(route('density.destroy', props.density.id))" class="bg-destructive text-white hover:bg-destructive/90">
                                Ya, Hapus
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="max-w-4xl grid grid-cols-1 gap-6">
            <!-- Card Informasi Umum -->
            <Card class="border-none shadow-lg">
                <CardHeader class="border-b bg-muted/20 pb-4">
                    <div class="flex items-center gap-2">
                        <IconHammer class="size-5 text-primary" />
                        <CardTitle class="text-lg">Informasi Density</CardTitle>
                    </div>
                </CardHeader>
                <CardContent class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">ID Record</span>
                        <p class="text-base font-medium mt-1">#{{ props.density.id }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal</span>
                        <p class="text-base font-medium mt-1">
                            {{ props.density.tgl ? new Date(props.density.tgl).toLocaleDateString("id-ID", { weekday: "long", day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Operator (Input Oleh)</span>
                        <p class="text-base font-medium text-zinc-700 dark:text-zinc-300 mt-1">
                            {{ props.density.user?.name ?? 'Tidak diketahui' }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

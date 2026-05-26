<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash, IconLoader2 } from "@tabler/icons-vue";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";

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

const form = useForm({
    tgl_test: props.chemical.tgl_test || "",
    kode_alkali: props.chemical.kode_alkali ?? "-",
    alkali_jam_mulai: props.chemical.alkali_jam_mulai ?? "00:00:00",
    alkali_jam_selesai: props.chemical.alkali_jam_selesai ?? "00:00:00",
    kode_acid: props.chemical.kode_acid ?? "-",
    acid_jam_mulai: props.chemical.acid_jam_mulai ?? "00:00:00",
    acid_jam_selesai: props.chemical.acid_jam_selesai ?? "00:00:00",
});
</script>

<template>
    <Head title="Edit Pengujian Chemical" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('chemical.show', props.chemical.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Data Pengujian</h2>
            </div>
        </div>

        <div class="max-w-3xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4 mb-4">
                    <CardTitle class="text-primary text-lg">Update Data ID: {{ props.chemical.id }}</CardTitle>
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive cursor-pointer"><IconTrash class="mr-2 size-4" />Hapus Log</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>
                                    Hapus permanen log pengujian tanggal
                                    <strong>{{ new Date(props.chemical.tgl_test).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}</strong>?
                                    Tindakan ini tidak dapat dibatalkan.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('chemical.destroy', props.chemical.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="form.put(route('chemical.update', props.chemical.id))" class="space-y-6">

                        <div class="grid gap-2 max-w-md">
                            <Label for="tgl_test">Tanggal Pengujian</Label>
                            <Input type="date" id="tgl_test" v-model="form.tgl_test" />
                            <p v-if="form.errors.tgl_test" class="text-sm text-destructive">{{ form.errors.tgl_test }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                            <div class="space-y-4 border p-4 rounded-lg bg-muted/20">
                                <h3 class="font-semibold text-blue-600 border-b pb-2">Parameter Alkali</h3>

                                <div class="grid gap-2">
                                    <Label for="kode_alkali">Kode Alkali</Label>
                                    <Input type="text" id="kode_alkali" v-model="form.kode_alkali" />
                                    <p v-if="form.errors.kode_alkali" class="text-sm text-destructive">{{ form.errors.kode_alkali }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="alkali_jam_mulai">Jam Mulai Alkali</Label>
                                    <Input type="text" id="alkali_jam_mulai" v-model="form.alkali_jam_mulai" />
                                    <p v-if="form.errors.alkali_jam_mulai" class="text-sm text-destructive">{{ form.errors.alkali_jam_mulai }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="alkali_jam_selesai">Jam Selesai Alkali</Label>
                                    <Input type="text" id="alkali_jam_selesai" v-model="form.alkali_jam_selesai" />
                                    <p v-if="form.errors.alkali_jam_selesai" class="text-sm text-destructive">{{ form.errors.alkali_jam_selesai }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 border p-4 rounded-lg bg-muted/20">
                                <h3 class="font-semibold text-purple-600 border-b pb-2">Parameter Acid</h3>

                                <div class="grid gap-2">
                                    <Label for="kode_acid">Kode Acid</Label>
                                    <Input type="text" id="kode_acid" v-model="form.kode_acid" />
                                    <p v-if="form.errors.kode_acid" class="text-sm text-destructive">{{ form.errors.kode_acid }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="acid_jam_mulai">Jam Mulai Acid</Label>
                                    <Input type="text" id="acid_jam_mulai" v-model="form.acid_jam_mulai" />
                                    <p v-if="form.errors.acid_jam_mulai" class="text-sm text-destructive">{{ form.errors.acid_jam_mulai }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="acid_jam_selesai">Jam Selesai Acid</Label>
                                    <Input type="text" id="acid_jam_selesai" v-model="form.acid_jam_selesai" />
                                    <p v-if="form.errors.acid_jam_selesai" class="text-sm text-destructive">{{ form.errors.acid_jam_selesai }}</p>
                                </div>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

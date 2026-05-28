<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    tgl_test: "",
    kode_alkali: "-",
    alkali_jam_mulai: "",
    alkali_jam_selesai: "",
    kode_acid: "-",
    acid_jam_mulai: "",
    acid_jam_selesai: "",
});

const formatTimeInput = (field: keyof typeof form, event: Event) => {
    const target = event.target as HTMLInputElement;
    let val = target.value.replace(/\D/g, ''); // Hapus semua karakter kecuali angka

    if (val.length > 4) val = val.substring(0, 4); // Maksimal 4 digit angka

    if (val.length > 2) {
        let hours = val.substring(0, 2);
        if (parseInt(hours) > 23) hours = '23'; // Maksimal jam 23

        let minutes = val.substring(2);
        if (parseInt(minutes) > 59) minutes = '59'; // Maksimal menit 59

        val = hours + ':' + minutes;
    } else if (val.length === 2 && parseInt(val) > 23) {
        val = '23';
    }

    // @ts-ignore
    form[field] = val;
};

</script>

<template>
    <Head title="Tambah Pengujian Chemical" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('chemical.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Data</h2>
        </div>

        <div class="max-w-3xl">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle class="text-primary">Master Data Pengujian Chemical</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="form.post(route('chemical.store'))" class="space-y-6">

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
                                    <Input type="text" id="alkali_jam_mulai" v-model="form.alkali_jam_mulai" @input="formatTimeInput('alkali_jam_mulai', $event)" placeholder="00:00" />
                                    <p v-if="form.errors.alkali_jam_mulai" class="text-sm text-destructive">{{ form.errors.alkali_jam_mulai }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="alkali_jam_selesai">Jam Selesai Alkali</Label>
                                    <Input type="text" id="alkali_jam_selesai" v-model="form.alkali_jam_selesai" @input="formatTimeInput('alkali_jam_selesai', $event)" placeholder="00:00" />
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
                                    <Input type="text" id="acid_jam_mulai" v-model="form.acid_jam_mulai" @input="formatTimeInput('acid_jam_mulai', $event)" placeholder="00:00" />
                                    <p v-if="form.errors.acid_jam_mulai" class="text-sm text-destructive">{{ form.errors.acid_jam_mulai }}</p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="acid_jam_selesai">Jam Selesai Acid</Label>
                                    <Input type="text" id="acid_jam_selesai" v-model="form.acid_jam_selesai" @input="formatTimeInput('acid_jam_selesai', $event)" placeholder="00:00" />
                                    <p v-if="form.errors.acid_jam_selesai" class="text-sm text-destructive">{{ form.errors.acid_jam_selesai }}</p>
                                </div>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data Baru
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

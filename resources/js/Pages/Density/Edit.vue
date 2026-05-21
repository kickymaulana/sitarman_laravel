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
    density: any;
}>();

const form = useForm({
    tgl: props.density.tgl
});
</script>

<template>
    <Head title="Edit Density" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('density.show', props.density.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Density</h2>
            </div>
        </div>

        <div class="max-w-xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4 mb-4">
                    <CardTitle class="text-primary text-lg">Update Data ID: {{ props.density.id }}</CardTitle>
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive cursor-pointer"><IconTrash class="mr-2 size-4" />Hapus</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>Hapus permanen log density tanggal {{ props.density.tgl }}?</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('density.destroy', props.density.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="form.put(route('density.update', props.density.id))" class="space-y-6">

                        <!-- Row Input: Tanggal -->
                        <div class="grid gap-2">
                            <Label for="tgl">Tanggal</Label>
                            <Input type="date" id="tgl" v-model="form.tgl" />
                            <p v-if="form.errors.tgl" class="text-sm text-destructive">{{ form.errors.tgl }}</p>
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

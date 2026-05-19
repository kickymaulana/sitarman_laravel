<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconDotsVertical,
    IconTrash,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    jamkeluaroven: { id: number; jam_keluar_oven: string };
}>();

const form = useForm({
    jam_keluar_oven: props.jamkeluaroven.jam_keluar_oven
});
</script>

<template>
    <Head title="Edit Jam Keluar Oven" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('jamkeluaroven.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Jam Keluar Oven</h2>
            </div>
        </div>
        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b">
                    <CardTitle class="text-primary text-lg">Update Data</CardTitle>

                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon">
                                    <IconDotsVertical class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive">
                                        <IconTrash class="mr-2 size-4" />Hapus
                                    </DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>
                                    Hapus permanen jam keluar oven
                                    <strong>{{ props.jamkeluaroven.jam_keluar_oven }}</strong>?
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction
                                    @click="router.delete(route('jamkeluaroven.destroy', props.jamkeluaroven.id))"
                                    class="bg-destructive text-white"
                                >
                                    Ya, Hapus
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6">
                    <form @submit.prevent="form.put(route('jamkeluaroven.update', props.jamkeluaroven.id))" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="jam_keluar_oven">Jam Keluar</Label>
                            <Input
                                id="jam_keluar_oven"
                                type="time"
                                v-model="form.jam_keluar_oven"
                            />
                            <p v-if="form.errors.jam_keluar_oven" class="text-sm text-destructive">
                                {{ form.errors.jam_keluar_oven }}
                            </p>
                        </div>
                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary">
                            <IconDeviceFloppy class="mr-2 size-4" />Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

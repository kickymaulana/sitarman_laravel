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
import { ref, computed, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });
const props = defineProps<{ 
    modelsize: { id: number; customer_id: number; modelsize: string };
    customers: Array<{ id: number; customer: string }>;
}>();

const form = useForm({ 
    customer_id: props.modelsize.customer_id,
    modelsize: props.modelsize.modelsize 
});

const searchQuery = ref("");
const showDropdown = ref(false);
const dropdownRef = ref(null);

onMounted(() => {
    const selected = props.customers.find(c => c.id === form.customer_id);
    if (selected) {
        searchQuery.value = selected.customer;
    }
});

onClickOutside(dropdownRef, () => {
    showDropdown.value = false;
    if (!form.customer_id) {
        searchQuery.value = "";
    } else {
        const selected = props.customers.find(c => c.id === form.customer_id);
        if (selected) searchQuery.value = selected.customer;
    }
});

const filteredCustomers = computed(() => {
    if (!searchQuery.value) return props.customers;
    return props.customers.filter((c) =>
        c.customer.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectCustomer = (customer: { id: number; customer: string }) => {
    form.customer_id = customer.id;
    searchQuery.value = customer.customer;
    showDropdown.value = false;
};
</script>

<template>
    <Head title="Edit Model/Size" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="rounded-full"
                    ><Link :href="route('modelsize.index')"
                        ><IconArrowLeft class="size-4" /></Link
                ></Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Model/Size</h2>
            </div>
        </div>
        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    class="flex flex-row items-center justify-between border-b"
                >
                    <CardTitle class="text-primary text-lg"
                        >Update Data</CardTitle
                    >
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child
                                ><Button variant="ghost" size="icon"
                                    ><IconDotsVertical class="size-4" /></Button
                            ></DropdownMenuTrigger>
                            <DropdownMenuContent align="end"
                                ><AlertDialogTrigger as-child
                                    ><DropdownMenuItem class="text-destructive"
                                        ><IconTrash
                                            class="mr-2 size-4"
                                        />Hapus</DropdownMenuItem
                                    ></AlertDialogTrigger
                                ></DropdownMenuContent
                            >
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader
                                ><AlertDialogTitle>Hapus Data?</AlertDialogTitle
                                ><AlertDialogDescription
                                    >Hapus permanen model/size
                                    <strong>{{ props.modelsize.modelsize }}</strong>?</AlertDialogDescription
                                ></AlertDialogHeader
                            >
                            <AlertDialogFooter
                                ><AlertDialogCancel>Batal</AlertDialogCancel
                                ><AlertDialogAction
                                    @click="
                                        router.delete(
                                            route(
                                                'modelsize.destroy',
                                                props.modelsize.id,
                                            ),
                                        )
                                    "
                                    class="bg-destructive text-white"
                                    >Ya, Hapus</AlertDialogAction
                                ></AlertDialogFooter
                            >
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>
                <CardContent class="pt-6">
                    <form
                        @submit.prevent="
                            form.put(route('modelsize.update', props.modelsize.id))
                        "
                        class="space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label for="customer_id">Customer</Label>
                            <div class="relative" ref="dropdownRef">
                                <Input
                                    v-model="searchQuery"
                                    @focus="showDropdown = true"
                                    placeholder="Cari & Pilih Customer..."
                                    class="w-full"
                                />
                                <div
                                    v-if="showDropdown"
                                    class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md border bg-popover text-popover-foreground shadow-md outline-none"
                                >
                                    <div
                                        v-if="filteredCustomers.length === 0"
                                        class="py-6 text-center text-sm text-muted-foreground"
                                    >
                                        Customer tidak ditemukan.
                                    </div>
                                    <div v-else class="p-1 bg-white dark:bg-zinc-900">
                                        <div
                                            v-for="customer in filteredCustomers"
                                            :key="customer.id"
                                            @click="selectCustomer(customer)"
                                            class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ customer.customer }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p
                                v-if="form.errors.customer_id"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.customer_id }}
                            </p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="modelsize">Model / Size</Label>
                            <Input
                                id="modelsize"
                                v-model="form.modelsize"
                                class=""
                            />
                        </div>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary"
                            ><IconDeviceFloppy class="mr-2 size-4" />Simpan
                            Perubahan</Button
                        >
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

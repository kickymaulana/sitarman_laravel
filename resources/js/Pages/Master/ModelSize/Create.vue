<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
} from "@tabler/icons-vue";
import { ref, computed } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    customers: Array<{ id: number; customer: string }>;
}>();

const form = useForm({ 
    customer_id: "",
    modelsize: "" 
});

const searchQuery = ref("");
const showDropdown = ref(false);
const dropdownRef = ref(null);

onClickOutside(dropdownRef, () => {
    showDropdown.value = false;
    // Reset search query if no valid customer selected
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
    <Head title="Tambah Model/Size" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full"
                ><Link :href="route('modelsize.index')"
                    ><IconArrowLeft class="size-4" /></Link
            ></Button>
            <h2 class="text-3xl font-bold tracking-tight">
                Tambah Model/Size
            </h2>
        </div>
        <div class="max-w-2xl">
            <Card class="border-none shadow-lg">
                <CardHeader
                    ><CardTitle class="text-primary"
                        >Master Data Model/Size</CardTitle
                    ></CardHeader
                >
                <CardContent>
                    <form
                        @submit.prevent="form.post(route('modelsize.store'))"
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
                                placeholder="Contoh: Model A - 42L / Size XL"
                                class=""
                            />
                            <p
                                v-if="form.errors.modelsize"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.modelsize }}
                            </p>
                        </div>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-primary hover:bg-primary/90"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<script setup lang="ts">


interface Event {
    title: string;
    description: string;
}

defineProps<{
    event: Event
}>()

</script>

<template>
    <div>
        <h1>{{ event.title }} </h1>
        <Switch />
    </div>
</template>

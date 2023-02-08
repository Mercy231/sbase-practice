<template>
    <div class="header">
        <div class="nav-bar">
            <h1><a href="/home">Home</a></h1>
        </div>
        <div class="user-bar">
            <h1><a href="/login">Login</a></h1>
        </div>
    </div>
    {{foo}}
</template>

<script setup>
import {ref} from "vue";

const props = defineProps({
    foo: String
});

</script>

<template>
    <div>
        <img :src="logoSrc" alt="Logo" />
    </div>
</template>

<script>
import logo from '@/assets/logo.jpg'; // Use import instead of require

export default {
    data() {
        return {
            logoSrc: logo // Set logoSrc to the imported logo
        };
    }
}
</script>

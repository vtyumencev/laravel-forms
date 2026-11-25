# @erag/livewire-forms-vue

Vue renderer for [erag/livewire-forms](https://github.com/eramitgupta/laravel-livewire-forms). Define the form in a Laravel class, render it with one component.

```bash
composer require erag/livewire-forms
npm install @erag/livewire-forms-vue
```

Let Tailwind CSS 4 scan the package (`resources/css/app.css`):

```css
@source "../../node_modules/@erag/livewire-forms-vue/dist";
```

```vue
<script setup lang="ts">
import { Form, type FormSchema } from '@erag/livewire-forms-vue';

defineProps<{ form: FormSchema }>();
</script>

<template>
    <Form
        :form="form"
        accent="#059669"
        @success="() => console.log('Saved')"
    />
</template>
```

Requires Vue 3.5+, `@inertiajs/vue3` 3, and Tailwind CSS 4.

Every field (select, date range, time, color, tags, file upload and more) is built in with Tailwind CSS, with no other UI library. Set the theme color with the `accent` prop or `Form::accent()` in PHP.

Documentation: **https://erag.in/laravel-livewire-forms/**

## Credits

Created and maintained by [Er Amit Gupta](https://github.com/eramitgupta) at [Erag Labs](https://github.com/erag-labs). MIT License © Amit Gupta.

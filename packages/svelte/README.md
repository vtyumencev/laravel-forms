# @erag/livewire-forms-svelte

Svelte 5 renderer for [erag/livewire-forms](https://github.com/eramitgupta/laravel-livewire-forms). Define the form in a Laravel class, render it with one component.

```bash
composer require erag/livewire-forms
npm install @erag/livewire-forms-svelte
```

Let Tailwind CSS 4 scan the package (`resources/css/app.css`):

```css
@source "../../node_modules/@erag/livewire-forms-svelte/dist";
```

```svelte
<script lang="ts">
    import { Form, type FormSchema } from '@erag/livewire-forms-svelte';

    let { form }: { form: FormSchema } = $props();
</script>

<Form
    {form}
    accent="#059669"
    onSuccess={() => console.log('Saved')}
/>
```

Requires Svelte 5, `@inertiajs/svelte` 3, and Tailwind CSS 4.

Every field (select, date range, time, color, tags, file upload and more) is built in with Tailwind CSS, with no other UI library. Set the theme color with the `accent` prop or `Form::accent()` in PHP.

Documentation: **https://erag.in/laravel-livewire-forms/**

## Credits

Created and maintained by [Er Amit Gupta](https://github.com/eramitgupta) at [Erag Labs](https://github.com/erag-labs). MIT License © Amit Gupta.

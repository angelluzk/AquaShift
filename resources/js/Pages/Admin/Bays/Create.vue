<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    branches: Array
});

const form = useForm({
    name: '',
    type: 'Padrão',
    branch_id: '',
    is_active: true
});

const submit = () => {
    form.post(route('admin.bays.store'));
};
</script>

<template>
    <Head title="Novo Box" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Criar Novo Box (Vaga)
                </h2>
                <Link 
                    :href="route('admin.bays.index')"
                    class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-md font-semibold text-xs uppercase"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        
                        <form @submit.prevent="submit" class="space-y-6">

                            <div>
                                <InputLabel for="branch_id" value="Filial (Obrigatório)" />
                                <select 
                                    id="branch_id" 
                                    v-model="form.branch_id" 
                                    required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="" disabled>Selecione uma filial</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.branch_id" />
                            </div>

                            <div>
                                <InputLabel for="name" value="Nome do Box" />
                                <TextInput 
                                    id="name" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    v-model="form.name" 
                                    required 
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="type" value="Tipo (Ex: Pequeno, Grande, Secagem)" />
                                <TextInput 
                                    id="type" 
                                    type="text" 
                                    class="mt-1 block w-full" 
                                    v-model="form.type"
                                />
                                <InputError class="mt-2" :message="form.errors.type" />
                            </div>
                            
                            <div class="block">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.is_active" name="is_active" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Box Ativo</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
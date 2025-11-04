<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    bays: Array
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.message);

const confirmDelete = (bayId) => {
    if (window.confirm('Tem certeza que deseja excluir este box?')) {
        useForm({}).delete(route('admin.bays.destroy', bayId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gerenciar Boxes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Gerenciamento de Boxes (Vagas)
                </h2>

                <Link 
                    :href="route('admin.bays.create')" 
                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-400 text-white dark:text-gray-800 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-indigo-500 dark:hover:bg-indigo-300"
                >
                    Novo Box
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div 
                            v-if="flashMessage" 
                            class="mb-4 p-4 rounded-md bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700"
                        >
                            <p class="text-sm font-medium text-green-800 dark:text-green-300">
                                {{ flashMessage }}
                            </p>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left ...">Nome do Box</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">Filial</th>
                                    <th scope="col" class="px-6 py-3 text-left ...">Status</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

                                <tr v-if="bays.length > 0" v-for="bay in bays" :key="bay.id">
                                    <td class="px-6 py-4 ...">{{ bay.name }}</td>
                                    <td class="px-6 py-4 ...">{{ bay.type }}</td>

                                    <td class="px-6 py-4 ...">{{ bay.branch ? bay.branch.name : 'N/A' }}</td>

                                    <td class="px-6 py-4 ...">
                                        <span :class="bay.is_active ? 'bg-green-100 ...' : 'bg-red-100 ...'">
                                            {{ bay.is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right ...">
                                        <Link :href="route('admin.bays.edit', bay.id)" class="text-indigo-600 ...">Editar</Link>
                                        <button @click="confirmDelete(bay.id)" class="ms-2 text-red-600 ...">Deletar</button>
                                    </td>
                                </tr>

                                <tr v-else>
                                    <td colspan="5" class="px-6 py-4 text-center ...">
                                        Nenhum box encontrado.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
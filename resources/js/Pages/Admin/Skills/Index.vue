<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Plus, Trash2, Sparkles, FolderPlus } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    categories: any[];
}>();

const newCategoryForm = useForm({
    name: '',
    sort_order: 0,
});

const newSkillForm = useForm({
    skill_category_id: props.categories[0]?.id || '',
    name: '',
    level: 85,
    sort_order: 0,
});

const submitCategory = () => {
    newCategoryForm.post('/admin/skills/categories', {
        onSuccess: () => newCategoryForm.reset()
    });
};

const submitSkill = () => {
    newSkillForm.post('/admin/skills', {
        onSuccess: () => newSkillForm.reset('name')
    });
};

const deleteCategory = (id: number) => {
    if (confirm('Delete this category and all its skills?')) {
        router.delete(`/admin/skills/categories/${id}`);
    }
};

const deleteSkill = (id: number) => {
    router.delete(`/admin/skills/${id}`);
};
</script>

<template>
    <div class="space-y-8 max-w-5xl mx-auto">
        <Head title="Skills Catalog – TZM Admin" />

        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Skills Catalog Management
            </h1>
            <p class="text-sm text-slate-500">
                Configure technology categories and proficiencies shown on your portfolio.
            </p>
        </div>

        <!-- Add Category & Add Skill Boxes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add Category -->
            <form @submit.prevent="submitCategory" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white text-sm">
                    <FolderPlus class="w-4 h-4 text-indigo-600" />
                    <span>Create Category</span>
                </div>
                <div class="flex gap-2">
                    <input
                        v-model="newCategoryForm.name"
                        required
                        type="text"
                        placeholder="E.g. Cloud & Infrastructure"
                        class="flex-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm"
                    />
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold text-xs shadow hover:bg-indigo-700">
                        Add
                    </button>
                </div>
            </form>

            <!-- Add Skill -->
            <form @submit.prevent="submitSkill" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white text-sm">
                    <Sparkles class="w-4 h-4 text-indigo-600" />
                    <span>Add Skill to Category</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <select v-model="newSkillForm.skill_category_id" required class="col-span-1 px-2 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs">
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <input
                        v-model="newSkillForm.name"
                        required
                        type="text"
                        placeholder="Skill Name"
                        class="col-span-1 px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs"
                    />
                    <div class="col-span-1 flex gap-1">
                        <input
                            v-model="newSkillForm.level"
                            type="number"
                            min="1"
                            max="100"
                            placeholder="%"
                            class="w-16 px-2 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-xs text-center"
                        />
                        <button type="submit" class="flex-1 px-3 py-2 rounded-lg bg-indigo-600 text-white font-semibold text-xs shadow hover:bg-indigo-700">
                            Add
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Categories & Skills Display -->
        <div class="space-y-6">
            <div
                v-for="cat in categories"
                :key="cat.id"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-4"
            >
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ cat.name }}</h2>
                    <button @click="deleteCategory(cat.id)" class="text-xs text-red-500 hover:underline">
                        Delete Category
                    </button>
                </div>

                <div class="grid max-h-[7.25rem] grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-4">
                    <div
                        v-for="skill in cat.skills"
                        :key="skill.id"
                        class="flex h-[3.25rem] items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 text-sm dark:border-slate-800 dark:bg-slate-800/50"
                    >
                        <div>
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ skill.name }}</p>
                            <span class="text-[10px] text-slate-400">{{ skill.level }}%</span>
                        </div>
                        <button type="button" class="text-slate-400 hover:text-red-500" @click="deleteSkill(skill.id)">
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, router, useForm} from '@inertiajs/vue3';
import {Attribute, AttributeType} from "@/types";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps<{
    attributeTypes: AttributeType[];
    attribute?: Attribute;
}>();

const form = useForm({
    title: props.attribute?.title ?? '',
    description: props.attribute?.description ?? '',
    attribute_type_id: props.attribute?.attribute_type_id ?? '',
});

function capitalizeFirstLetter(string: string) {
    return string[0].toUpperCase() + string.slice(1);
}

const submit = () => {
    if (props.attribute) {
        router.put(route('attributes.update', props.attribute.id), form.data());
    } else {
        router.post(route('attributes.store'), form.data());
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Update Attribute"/>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="title" value="Title"/>

                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.title"/>
            </div>


            <div class="w-96 mt-4">
                <div class="relative w-full min-w-[200px] mt-6">
                    <textarea
                        required
                        v-model="form.description"
                        class="peer h-full min-h-[100px] w-full resize-none rounded-[7px] border border-blue-gray-200 border-t-transparent bg-transparent px-3 py-2.5 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-gray-900 focus:border-t-transparent focus:outline-0 disabled:resize-none disabled:border-0 disabled:bg-blue-gray-50"
                        rows="5"></textarea>
                    <InputError class="mt-2" :message="form.errors.description"/>
                    <label
                        class="before:content[' '] after:content[' '] pointer-events-none absolute left-0 -top-1.5 flex h-full w-full select-none text-[11px] font-normal leading-tight text-blue-gray-400 transition-all before:pointer-events-none before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5 before:w-2.5 before:rounded-tl-md before:border-t before:border-l before:border-blue-gray-200 before:transition-all after:pointer-events-none after:mt-[6.5px] after:ml-1 after:box-border after:block after:h-1.5 after:w-2.5 after:flex-grow after:rounded-tr-md after:border-t after:border-r after:border-blue-gray-200 after:transition-all peer-placeholder-shown:text-sm peer-placeholder-shown:leading-[3.75] peer-placeholder-shown:text-blue-gray-500 peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent peer-focus:text-[11px] peer-focus:leading-tight peer-focus:text-gray-900 peer-focus:before:border-t-2 peer-focus:before:border-l-2 peer-focus:before:border-gray-900 peer-focus:after:border-t-2 peer-focus:after:border-r-2 peer-focus:after:border-gray-900 peer-disabled:text-transparent peer-disabled:before:border-transparent peer-disabled:after:border-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500">
                        Description
                    </label>
                </div>
            </div>
            <div class="w-96 mt-4">
                <input-label for="attribute_type_id" value="Attribute Type"/>
                <select
                    v-model="form.attribute_type_id"
                    :class="`bg-${props.attributeTypes.find(attributeType=>attributeType.id===form.attribute_type_id)?.color}-100`"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                    <option :class="`bg-${attributeType.color}-100`" v-for="attributeType in props.attributeTypes"
                            :value="attributeType.id"
                            :key="attributeType.id">
                        {{ attributeType.title }}
                    </option>
                </select>
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link
                    v-if="props.attribute"
                    :href="route('attributes.destroy', props.attribute.id)"
                    method="delete"
                    class="inline-flex h-full items-center w-min text-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                > Delete Attribute </Link>
                <PrimaryButton class="ms-4 w-min" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Update attribute
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

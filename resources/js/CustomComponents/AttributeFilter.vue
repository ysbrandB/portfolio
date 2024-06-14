<script setup lang="ts">

import OpeningComponent from "@/CustomComponents/OpeningComponent.vue";
import Pill from "@/CustomComponents/Pill.vue";
import {Attribute, AttributeType} from "@/types";
import {nextTick, ref} from "vue";

const props = withDefaults(defineProps<{
    attributeTypes: AttributeType[],
    checkedAttributes?: {},
    title?: string
}>(),{
    title: 'filters',
    checkedAttributes: {}
});

const emit = defineEmits(['update:checkedAttributes']);

const checkedAttributes = ref(props.checkedAttributes);
console.log(props.checkedAttributes)
console.log(props.title)
const check = (attributeType: { id: number, color: string }, attribute: {
    id: number
}, checked: boolean) => nextTick(() => {
    console.log(checkedAttributes.value);
    if (checked) {
        if (!Object.hasOwn(checkedAttributes.value, attributeType.id)) {
            checkedAttributes.value[attributeType.id] = {
                color: attributeType.color,
                attributes: []
            };
        }
        checkedAttributes.value[attributeType.id].attributes.push(attribute);
    } else {
        checkedAttributes.value[attributeType.id].attributes = checkedAttributes.value[attributeType.id].attributes.filter((oldAttribute: Attribute) => oldAttribute.id !== attribute.id);
    }
    //filter out the attribute type if there are no attributes
    //@ts-ignore
    checkedAttributes.value = Object.fromEntries(Object.entries(checkedAttributes.value).filter(([key, value]) => value.attributes.length > 0));
    emit('update:checkedAttributes', checkedAttributes.value);
});

function capitalizeFirstLetter(string: string) {
    return string[0].toUpperCase() + string.slice(1);
}
</script>
<template>
    <div class="w-full text-2xl text-center font-semibold mt-4">{{ capitalizeFirstLetter(title) }}</div>
    <div
        class="mx-2 mt-1 flex flex-wrap text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <template v-for="(attributeType, idx) in checkedAttributes">
            <pill class="cursor-pointer" @click="check({id:idx, color:attributeType.color}, attribute, false)"
                  v-for="attribute in attributeType.attributes" :key="attribute.id"
                  :color="attributeType.color">
                {{ attribute.title }} <span class="ms-2 text-red-600">x</span>
            </pill>
        </template>
        <div v-if="Object.entries(checkedAttributes).length<=0" class="w-full p-2 text-center text-gray-500">
            No {{ title }} applied
        </div>
    </div>
    <OpeningComponent v-for="attributeType in attributeTypes" :title="attributeType.title">
        <template #title>
            <div class="font-bold text-sm md:text-lg p-1 md:p3 rounded rounded-lg"
                 :class="`bg-${attributeType.color}-100`">{{ attributeType.title }}
            </div>
        </template>
        <template #content>
            <ul class="w-full mt-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <li v-for="attribute in attributeType.attributes" :key="attribute.id"
                    class="w-full border-b border-gray-200 rounded-t-lg dark:border-gray-600">
                    <div class="flex items-center ps-3">
                        <input type="checkbox"
                               :checked="Object.hasOwn(checkedAttributes, attributeType.id) && checkedAttributes[attributeType.id].attributes.some((checkedAttribute: Attribute) => checkedAttribute.id === attribute.id)"
                               @change="check(attributeType, attribute, ($event as HTMLInputElement|any).target.checked)"
                               :id="attribute.id.toString()" :value="attribute.id"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label :for="attribute.id.toString()"
                               class="w-full py-2 md:py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{
                                attribute.title
                            }}</label>
                    </div>
                </li>
            </ul>
        </template>
    </OpeningComponent>
</template>
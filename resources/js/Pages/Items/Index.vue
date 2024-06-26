<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import {router} from "@inertiajs/vue3";
import {AttributeType, Item, Question} from "@/types";
import Card from "@/Components/Card.vue";
import AttributeFilter from "@/CustomComponents/AttributeFilter.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {computed, onMounted, Ref, ref, watch, toRaw} from "vue";
import axios from "axios";
import ItemCard from "@/Pages/Items/ItemCard.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SelectedItemDropdown from "@/CustomComponents/SelectedItemDropdown.vue";
import {Breakpoints} from '@/CustomComponents/TailwindWidth'
import MountedTeleport from "@/CustomComponents/MountedTeleport.vue";
import breakpoint from "../../CustomComponents/TailwindWidth";


const props = defineProps<{
    items: Item[],
    attributeTypes: AttributeType[],
    initialFilters: Record<number, string[]>,
    questions: Question[],
    initialSelectedItems: Item[],
    explainer: boolean,
}>();
const selectedItems = ref(new Set<Item>(toRaw(props.initialSelectedItems) ?? []));
const selectedItemIds = computed(() => Array.from(selectedItems.value).map((item) => item.id));
const reloadWithFilters = (filters: Record<number, number[]>) => {
    router.reload({
        data: {
            filters: filters
        },
        only: ['items']
    })
};

onMounted(() => {
    watch(selectedItems.value, () => {
        axios.post(route('graph.syncSelected'), {
            selected: selectedItemIds.value
        });
    })
});


const resetChoice = () => {
    choiceOpen.value = false;
    choiceIndex.value = 0;
}

const questionAnswered = (filters: number[]) => {
    filter.value?.addFiltersByAttributeIds(filters);
    if (choiceIndex.value < props.questions.length - 1) {
        choiceIndex.value++;
        return;
    }
    resetChoice();
}

const choiceOpen = ref(false);
const choiceIndex = ref(0);
const explainOpen = ref(props.explainer);
const question = computed(() => props.questions[choiceIndex.value]);
const filter: Ref<typeof AttributeFilter | null> = ref(null);

const removeItemFromSelected = (id: number) => {
    const item = Array.from(selectedItems.value).find((item) => item.id === id);
    if (item) {
        selectedItems.value.delete(item);
    }
}

const addItemToSelected = (id: number) => {
    const item = props.items.find((item) => item.id === id);
    if (item) {
        selectedItems.value.add(item);
    }
}
const phoneModalOpen = ref(false);
const lastBreakpoint = ref(breakpoint.value);
watch(breakpoint, (br) => {
    if (breakpoint.value === Breakpoints.sm && lastBreakpoint.value !== Breakpoints.sm) {
        phoneModalOpen.value = false;
        lastBreakpoint.value = Breakpoints.sm;
    } else {
        lastBreakpoint.value = breakpoint.value;
    }
});
const shouldModal = computed(() => breakpoint.value === Breakpoints.sm);
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Items</h2>
                <div class="flex flex-row gap-2 justify-end">
                    <div v-if="$page.props.auth.user">
                        <NavLink :href="route('items.create')">
                            New item
                        </NavLink>
                    </div>
                    <secondary-button @click="explainOpen=true">
                        Explainer
                    </secondary-button>
                    <primary-button
                        @click="resetChoice(); filter?.reset(); choiceOpen=true">
                        Choice helper
                    </primary-button>
                    <selected-item-dropdown class="mx-2" :selected-items="selectedItems"/>
                </div>
            </div>
        </template>

        <section class="relative w-full h-full mx-auto">
            <button class="fixed bottom-0 right-0" v-if="shouldModal"
                    @click="phoneModalOpen=true">
                <card class="font-bold" :additional-classes="'mr-2 shadow p-2 border-dashed border-2 border-gray-700'">
                    Filters
                </card>
            </button>
            <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-12  gap-4 max-w-[90%] mx-auto">
                <div class="col-span-3 md:pt-0 mt-2">
                    <MountedTeleport to="#attributeModal" :disabled="!shouldModal">
                        <card class="">
                            <div class="w-full text-center">
                                <attribute-filter
                                    ref="filter"
                                    @update="reloadWithFilters"
                                    :attribute-types="attributeTypes"
                                    :initial-filters="initialFilters"/>
                            </div>
                        </card>
                    </MountedTeleport>
                </div>
                <div class="lg:col-span-9 col-span-3">
                    <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-4 mt-4">
                        <div class="md:mx-0 md:max-w-full max-w-[70%] w-full mx-auto" v-for="item in items">
                            <item-card :item="item">
                                <template #qr>
                                    <button v-if="selectedItemIds.find((id) => id === item.id)"
                                            @click="removeItemFromSelected(item.id)"
                                            class="bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                            style="border-radius:.5em;font-size: 1.1em; width:3.5em; height:3em; align-self: flex-end;">
                                        -
                                    </button>
                                    <button v-else @click="addItemToSelected(item.id)"
                                            class="bg-grey-100 dark:bg-gray-800 border border-grey-300 dark:border-gray-500 font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-emerald-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                            style="border-radius:.5em;font-size: 1.1em; width:3.5em; height:3em; align-self: flex-end;">
                                        +
                                    </button>
                                </template>
                            </item-card>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <modal :show="choiceOpen" @close="resetChoice">
            <div class="w-full bg-emerald-100 p-8">
                <div class="text-black text-2xl font-bold">Choice helper</div>
                <div class="text-gray-900 text-lg mt-1">
                    Answer the questions to see which items fit your needs!
                </div>
                <card class="mt-4" v-if="question">
                    <div class="text-gray-800 dark:text-gray-200 leading-tight">{{ question.description }}</div>
                    <div class="w-full mt-2 mx-auto" v-for="answer in question.answers">
                        <primary-button @click="questionAnswered(answer.attributes.map(attr=>attr.id))">
                            {{ answer.text }}
                        </primary-button>
                    </div>
                </card>
                <card v-else>
                    <div class="text-gray-800 dark:text-gray-200 leading-tight">
                        There currently are no questions available.
                    </div>
                </card>
            </div>
        </modal>

        <modal :show="explainOpen" @close="explainOpen=false">
            <div class="w-full bg-blue-100 p-8">
                <div class="text-black text-2xl font-bold">Choice helper</div>
                <div class="text-gray-900 text-lg mt-1">
                    This tool helps you find the right sensors and actuators for your project!
                </div>
                <card class="mt-4">
                    <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                        What to do?
                    </div>
                    <div class="text-gray-800 dark:text-gray-200">
                        You can add items to your selection by clicking on the plus button.<br>
                        You can see your selected items in the top right!
                        Click on an item card to learn more about it.
                        <br><br>
                    </div>

                    <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                        Which items to choose?
                    </div>
                    <div class="text-gray-800 dark:text-gray-200">
                        If you are unsure which items to choose, you can use the choice helper to answer a few questions
                        and narrow down the selection.
                        You can also always find it with the button at the top of this page!<br> <br>
                    </div>

                    <div class="font-bold text-lg text-gray-900 dark:text-gray-100">
                        Then what?
                    </div>
                    <div class="text-gray-800 dark:text-gray-200">
                        When you are done, see how your selected items can be put together by clicking on the "ITEM
                        GRAPH" button.
                    </div>
                </card>
                <div class="w-full flex justify-between gap-4 mt-6">
                    <secondary-button @click="explainOpen=false">
                        I want to explore on my own
                    </secondary-button>
                    <primary-button @click="choiceOpen=true; explainOpen=false">
                        Use the choice helper
                    </primary-button>
                </div>
            </div>
        </modal>
        <modal :show="shouldModal && phoneModalOpen" @close="phoneModalOpen=false">
            <div class="attributeModal" id="attributeModal">
                <!--            the attribute filter will be put here when the screen is in the mobile range-->
            </div>
        </modal>
    </AuthenticatedLayout>
</template>

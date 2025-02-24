<template>
    <div class="mt-6">
        <Head title="Lucky Page" />
        <div class="overflow-hidden max-w-7xl mx-auto p-6">
            <h1 class="text-2xl font-semibold text-center mb-6 text-white">Lucky Game Page</h1>

            <div class="space-y-4">
                <!-- Generate Link Button -->
                <div class="flex justify-center">
                    <Button
                        @click="generateLink"
                        class="ms-4"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        Generate New Link
                    </Button>
                </div>

                <!-- Deactivate Link Button -->
                <div class="flex justify-center">
                    <Button
                        @click="deactivateLink"
                        class="ms-4"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        Deactivate Link
                    </Button>
                </div>

                <!-- Play Game Button -->
                <div class="flex justify-center">
                    <Button
                        @click="playLuckyGame"
                        class="ms-4"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        I'm Feeling Lucky
                    </Button>
                </div>

                <!-- History Button -->
                <div class="flex justify-center">
                    <Button
                        @click="getHistory"
                        class="ms-4"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        Show History
                    </Button>
                </div>
            </div>

            <!-- Game Result Section -->
            <div v-if="gameResult" class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-2">Game Result</h3>
                <p>Number: <strong>{{ gameResult.random_number }}</strong></p>
                <p>Result: <strong>{{ gameResult.result }}</strong></p>
                <p>Winning Amount: <strong>{{ gameResult.win_amount }}</strong></p>
            </div>

            <!-- History Section -->
            <div v-if="historyList.length" class="mt-6">
                <h3 class="text-xl font-semibold mb-4">Last 3 Results:</h3>
                <ul class="space-y-2">
                    <li v-for="(item, index) in historyList" :key="index" class="bg-gray-50 p-4 rounded-lg shadow-md">
                        <p><strong>Number:</strong> {{ item.random_number }} | <strong>Result:</strong> {{ item.result }} | <strong>Win Amount:</strong> {{ item.win_amount }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import Button from '@/Components/PrimaryButton.vue';

export default {
    layout: Layout,
    components: {
        Head,
        Link,
        Button,
    },
    props: {
        token: String,
        flashMessage: String,
    },
    data() {
        return {
            gameResult: null,
            historyList: [],
            processing: false,
        };
    },
    methods: {
        generateLink() {
            if (confirm('Are you sure?')) {
                this.processing = true;
                this.historyList = [];
                this.$inertia.post(`/lucky/${this.token}/generate-link`, {},{
                    onSuccess: () => this.processing = false,
                });
            }
        },
        deactivateLink() {
            if (confirm('Are you sure?')) {
                this.processing = true;
                this.$inertia.post(`/lucky/${this.token}/deactivate-link`, {}, {
                    onSuccess: () => this.processing = false,
                });
            }
        },
        playLuckyGame() {
            this.processing = true;
            this.historyList = [];
            axios.post(`/lucky/${this.token}/play`)
                .then((res) => {
                    this.gameResult = res.data.gameResult;
                })
                .catch((error) => {
                    console.error('Error playing lucky game:', error);
                })
                .finally(() => {
                    this.processing = false;
                });
        },
        getHistory() {
            this.rocessing = true;
            axios.post(`/lucky/${this.token}/history`)
                .then((res) => {
                    this.historyList = res.data.history;
                })
                .catch((error) => {
                    console.error('Error fetching history:', error);
                })
                .finally(() => {
                    this.processing = false;
                });
        },
    },
};
</script>

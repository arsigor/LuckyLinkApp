<template>
    <!-- Success notification -->
    <div v-if="$page.props.flash.success && show" aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="show" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">Successfully!</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $page.props.flash.success }}</p>
                            </div>
                            <div class="ml-4 flex shrink-0">
                                <button type="button" @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <!-- Error notification -->
    <div v-if="($page.props.flash.error || Object.keys($page.props.errors).length > 0) && show" aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            <transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" enter-to-class="translate-y-0 opacity-100 sm:translate-x-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="show" class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-red-500 shadow-lg ring-1 ring-black/5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-white">Error!</p>
                                <div v-if="$page.props.flash.error" class="mt-1 text-sm">{{ $page.props.flash.error }}</div>
                                <div v-else class="mt-1 text-sm">
                                    <span v-if="Object.keys($page.props.errors).length === 1">There is one form error.</span>
                                    <span v-else>There are {{ Object.keys($page.props.errors).length }} form errors.</span>
                                </div>
                            </div>
                            <div class="ml-4 flex shrink-0">
                                <button type="button" @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
// import { CheckCircleIcon } from '@heroicons/vue/24/outline'
// import { XMarkIcon } from '@heroicons/vue/20/solid'

export default {
    components: {
        // CheckCircleIcon,
        // XMarkIcon,
    },
    data() {
        return {
            show: true,
            hideTimeout: null, // Reference for the timeout
        }
    },
    watch: {
        '$page.props.flash': {
            handler() {
                this.showNotification()
            },
            deep: true,
        },
    },
    methods: {
        showNotification() {
            this.show = true;

            // Clear any previous timeout to avoid overlapping
            if (this.hideTimeout) {
                clearTimeout(this.hideTimeout);
            }

            // Set a timeout to hide the notification
            this.hideTimeout = setTimeout(() => {
                this.show = false;
            }, 5000); // Adjust time (5000ms = 5 seconds) as needed
        },
    },
    mounted() {
        // Ensure notification is displayed if present initially
        if (this.$page.props.flash.success || this.$page.props.flash.error || Object.keys(this.$page.props.errors).length > 0) {
            this.showNotification();
        }
    },
}
</script>

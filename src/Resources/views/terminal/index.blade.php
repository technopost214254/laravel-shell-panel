<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.account.edit.title')
    </x-slot>

    <v-terminal />
    
        @pushOnce('scripts')
            <script
                type="text/x-template"
                id="v-terminal-template"
            >
                <div class="flex flex-col justify-center">
                    
                    <div class="p-4">
                        <h2 class="text-2xl font-bold mb-4">Laravel Command Runner</h2>
                        
                        <!-- Command Selection -->
                        <div class="mb-4">
                            <select 
                                v-model="selectedCommand" 
                                class="w-full p-2 border rounded"
                                @change="handleCommandSelect"
                            >
                                <option value="">Select a command</option>
                                <option v-for="command in commands" :key="command.name" :value="command">
                                    @{{ command.name }} - @{{ command.description }}
                                </option>
                            </select>
                        </div>

                        <!-- Arguments and Options -->
                        <div v-if="selectedCommand" class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">Arguments</h3>

                            <div 
                                v-for="(arg, name) in selectedCommand.arguments" 
                                :key="name" 
                                class="mb-2"
                            >
                                <label class="block text-sm font-medium">
                                    @{{ name }}
                                </label>
                                
                                <input 
                                    v-model="commandArgs[name]" 
                                    class="w-full p-2 border rounded"
                                    :placeholder="arg.description"
                                >
                            </div>

                            <h3 class="text-lg font-semibold mb-2 mt-4">
                                Options
                            </h3>

                            <div 
                                v-for="(option, name) in selectedCommand.options" 
                                :key="name" 
                                class="mb-2"
                            >
                                <div class="flex items-center mb-1">
                                    <input 
                                        type="checkbox" 
                                        v-model="commandOptions[`--${name}`]"
                                        class="mr-2"
                                        :id="'option-' + name"
                                    >
                                    <label :for="'option-' + name" class="text-sm font-medium">
                                        --@{{ name }}
                                    </label>
                                </div>

                                <input 
                                    v-if="option.acceptValue && commandOptions[`--${name}`]" 
                                    v-model="optionValues[`--${name}`]"
                                    class="w-full p-2 border rounded mt-1"
                                    :placeholder="option.description"
                                >
                            </div>
                        </div>

                        <!-- Execute Button -->
                        <button 
                            @click="executeCommand" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                            :disabled="!selectedCommand"
                        >
                            Execute Command
                        </button>

                        <!-- Command Preview -->
                        <div v-if="selectedCommand" class="mt-4">
                            <h3 class="text-lg font-semibold mb-2">Command Preview</h3>

                            <pre class="bg-gray-100 p-4 rounded overflow-x-auto">
                                @{{ commandPreview }}
                            </pre>
                        </div>

                        <!-- Output Display -->
                        <div v-if="output" class="mt-4">
                            <h3 class="text-lg font-semibold mb-2">Output</h3>

                            <pre class="bg-gray-100 p-4 rounded overflow-x-auto">
                                @{{ output }}
                            </pre>
                        </div>

                        <!-- Error Display -->
                        <div v-if="error" class="mt-4 text-red-600 p-4 bg-red-50 rounded">
                            @{{ error }}
                        </div>
                           
                    </div>
                </div>
            </script>

            <script type="module">
                app.component('v-terminal', {
                    template: '#v-terminal-template',

                    data() {
                        return {
                            commands: [],
                            selectedCommand: null,
                            commandArgs: {},
                            commandOptions: {},
                            optionValues: {},
                            output: '',
                            error: ''
                        }
                    },

                    mounted() {
                        try {
                            this.$axios
                                .get(`{{ route('admin.commands.index') }}`)
                                .then(response => {
                                    this.commands = response.data;
                                })
                                .catch(error => {});
                        } catch (error) {
                            this.error = 'Failed to load commands'
                        }
                    },

                    methods: {
                        handleCommandSelect() {
                            this.commandArgs = {}
                            this.commandOptions = {}
                            this.optionValues = {}
                            this.output = ''
                            this.error = ''
                        },

                        executeCommand() {
                            try {
                                this.output = ''
                                this.error = ''

                                const options = {}
                                
                                for (const [key, value] of Object.entries(this.commandOptions)) {
                                    if (value) {
                                        options[key] = this.optionValues[key] || true
                                    }
                                }

                                this.$axios
                                    .post(`{{ route('admin.commands.execute') }}`, {
                                        params: { 
                                            command: this.selectedCommand.name,
                                            arguments: this.commandArgs,
                                            options: options
                                        }
                                    })
                                    .then(response => {
                                        this.output = response.data.output;
                                    })
                                    .catch(error => {});
                            } catch (error) {
                                this.error = error.response?.data?.error || 'Failed to execute command'
                            }
                        }
                    },
                });
            </script>
        @endPushOnce
</x-admin::layouts>
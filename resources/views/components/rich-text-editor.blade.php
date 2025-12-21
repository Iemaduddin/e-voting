@props([
    'name' => 'content',
    'label' => 'Konten',
    'value' => '',
    'required' => false,
    'helperText' => null,
    'placeholder' => 'Tulis sesuatu...',
])

<div x-data="{
    content: @entangle($attributes->wire('model')),
    showLinkDialog: false,
    linkUrl: '',
    init() {
        this.$refs.editor.innerHTML = this.content || '';
        // Watch for content changes from Livewire
        this.$watch('content', (value) => {
            if (this.$refs.editor.innerHTML !== value) {
                this.$refs.editor.innerHTML = value || '';
            }
        });
    },
    execCommand(command, value = null) {
        document.execCommand(command, false, value);
        this.updateContent();
    },
    updateContent() {
        this.content = this.$refs.editor.innerHTML;
    },
    insertLink() {
        if (this.linkUrl) {
            this.execCommand('createLink', this.linkUrl);
            this.showLinkDialog = false;
            this.linkUrl = '';
        }
    },
    insertImage() {
        const url = prompt('Masukkan URL gambar:');
        if (url) {
            this.execCommand('insertImage', url);
        }
    },
    clearFormatting() {
        this.execCommand('removeFormat');
    },
    isActive(command) {
        return document.queryCommandState(command);
    }
}" class="w-full">

    <!-- Label -->
    @if ($label)
        <x-input-label :for="$name" :value="$label" class="text-gray-700 font-semibold mb-2" :required="$required" />
    @endif

    <!-- Toolbar -->
    <div class="bg-gray-50 border border-gray-300 rounded-t-lg p-2">
        <div class="flex flex-wrap gap-1">
            <!-- Text Formatting -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <!-- Bold -->
                <button type="button" @click="execCommand('bold')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('bold') }" title="Bold (Ctrl+B)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M5.246 3.744a.75.75 0 0 1 .75-.75h7.125a4.875 4.875 0 0 1 3.346 8.422 5.25 5.25 0 0 1-2.97 9.58h-7.5a.75.75 0 0 1-.75-.75V3.744Zm7.125 6.75a2.625 2.625 0 0 0 0-5.25H8.246v5.25h4.125Zm-4.125 2.251v6h4.5a3 3 0 0 0 0-6h-4.5Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>

                <!-- Italic -->
                <button type="button" @click="execCommand('italic')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('italic') }" title="Italic (Ctrl+I)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M10.497 3.744a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-3.275l-5.357 15.002h2.632a.75.75 0 1 1 0 1.5h-7.5a.75.75 0 1 1 0-1.5h3.275l5.357-15.002h-2.632a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>

                <!-- Underline -->
                <button type="button" @click="execCommand('underline')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('underline') }" title="Underline (Ctrl+U)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M5.995 2.994a.75.75 0 0 1 .75.75v7.5a5.25 5.25 0 1 0 10.5 0v-7.5a.75.75 0 0 1 1.5 0v7.5a6.75 6.75 0 1 1-13.5 0v-7.5a.75.75 0 0 1 .75-.75Zm-3 17.252a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5h-16.5a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>

                <!-- Strikethrough -->
                <button type="button" @click="execCommand('strikeThrough')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('strikeThrough') }" title="Strikethrough">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M9.657 4.728c-1.086.385-1.766 1.057-1.979 1.85-.214.8.046 1.733.81 2.616.746.862 1.93 1.612 3.388 2.003.07.019.14.037.21.053h8.163a.75.75 0 0 1 0 1.5h-8.24a.66.66 0 0 1-.02 0H3.75a.75.75 0 0 1 0-1.5h4.78a7.108 7.108 0 0 1-1.175-1.074C6.372 9.042 5.849 7.61 6.229 6.19c.377-1.408 1.528-2.38 2.927-2.876 1.402-.497 3.127-.55 4.855-.086A8.937 8.937 0 0 1 16.94 4.6a.75.75 0 0 1-.881 1.215 7.437 7.437 0 0 0-2.436-1.14c-1.473-.394-2.885-.331-3.966.052Zm6.533 9.632a.75.75 0 0 1 1.03.25c.592.974.846 2.094.55 3.2-.378 1.408-1.529 2.38-2.927 2.876-1.402.497-3.127.55-4.855.087-1.712-.46-3.168-1.354-4.134-2.47a.75.75 0 0 1 1.134-.982c.746.862 1.93 1.612 3.388 2.003 1.473.394 2.884.331 3.966-.052 1.085-.384 1.766-1.056 1.978-1.85.169-.628.046-1.33-.381-2.032a.75.75 0 0 1 .25-1.03Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>
            </div>

            <!-- Text Alignment -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <!-- Align Left -->
                <button type="button" @click="execCommand('justifyLeft')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('justifyLeft') }" title="Align Left">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm0 4h10v2H3V8zm0 4h14v2H3v-2zm0 4h10v2H3v-2z" />
                    </svg>
                </button>

                <!-- Align Center -->
                <button type="button" @click="execCommand('justifyCenter')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('justifyCenter') }" title="Align Center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm2 4h10v2H5V8zm-2 4h14v2H3v-2zm2 4h10v2H5v-2z" />
                    </svg>
                </button>

                <!-- Align Right -->
                <button type="button" @click="execCommand('justifyRight')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('justifyRight') }" title="Align Right">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm4 4h10v2H7V8zm-4 4h14v2H3v-2zm4 4h10v2H7v-2z" />
                    </svg>
                </button>

                <!-- Justify -->
                <button type="button" @click="execCommand('justifyFull')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('justifyFull') }" title="Justify">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm0 4h14v2H3V8zm0 4h14v2H3v-2zm0 4h14v2H3v-2z" />
                    </svg>
                </button>
            </div>

            <!-- Lists -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <!-- Ordered List -->
                <button type="button" @click="execCommand('insertOrderedList')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('insertOrderedList') }" title="Numbered List">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4h2v2H4V4zm0 4h2v2H4V8zm0 4h2v2H4v-2zm4-8h10v2H8V4zm0 4h10v2H8V8zm0 4h10v2H8v-2z" />
                    </svg>
                </button>

                <!-- Unordered List -->
                <button type="button" @click="execCommand('insertUnorderedList')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-600': isActive('insertUnorderedList') }" title="Bullet List">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M4 5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1zm0 4c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1zm0 4c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1zm4-8h10v2H8V5zm0 4h10v2H8V9zm0 4h10v2H8v-2z" />
                    </svg>
                </button>

                <!-- Indent -->
                <button type="button" @click="execCommand('indent')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Indent">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm0 4v6l3-3-3-3zm4 0h10v2H7V8zm0 4h10v2H7v-2zm-4 4h14v2H3v-2z" />
                    </svg>
                </button>

                <!-- Outdent -->
                <button type="button" @click="execCommand('outdent')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Outdent">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4h14v2H3V4zm3 4l-3 3 3 3V8zm1 0h10v2H7V8zm0 4h10v2H7v-2zm-4 4h14v2H3v-2z" />
                    </svg>
                </button>
            </div>

            <!-- Heading -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <select @change="execCommand('formatBlock', $event.target.value); $event.target.value = ''"
                    class="px-2 py-1 border-0 bg-transparent rounded hover:bg-gray-200 transition-colors duration-150 text-sm font-medium cursor-pointer">
                    <option value="">Format</option>
                    <option value="h1">Heading 1</option>
                    <option value="h2">Heading 2</option>
                    <option value="h3">Heading 3</option>
                    <option value="h4">Heading 4</option>
                    <option value="h5">Heading 5</option>
                    <option value="h6">Heading 6</option>
                    <option value="p">Paragraph</option>
                </select>
            </div>

            <!-- Insert -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <!-- Link -->
                <button type="button" @click="showLinkDialog = true"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Insert Link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                            d="M19.902 4.098a3.75 3.75 0 0 0-5.304 0l-4.5 4.5a3.75 3.75 0 0 0 1.035 6.037.75.75 0 0 1-.646 1.353 5.25 5.25 0 0 1-1.449-8.45l4.5-4.5a5.25 5.25 0 1 1 7.424 7.424l-1.757 1.757a.75.75 0 1 1-1.06-1.06l1.757-1.757a3.75 3.75 0 0 0 0-5.304Zm-7.389 4.267a.75.75 0 0 1 1-.353 5.25 5.25 0 0 1 1.449 8.45l-4.5 4.5a5.25 5.25 0 1 1-7.424-7.424l1.757-1.757a.75.75 0 1 1 1.06 1.06l-1.757 1.757a3.75 3.75 0 1 0 5.304 5.304l4.5-4.5a3.75 3.75 0 0 0-1.035-6.037.75.75 0 0 1-.354-1Z"
                            clip-rule="evenodd" />
                    </svg>

                </button>

                <!-- Image -->
                <button type="button" @click="insertImage()"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Insert Image">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M4 3h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1zm12 11V6H4v8l3-3 3 3 2-2 4 4zm-7-5a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>

                <!-- Horizontal Rule -->
                <button type="button" @click="execCommand('insertHorizontalRule')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150"
                    title="Insert Horizontal Line">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 10h14v2H3v-2z" />
                    </svg>
                </button>
            </div>

            <!-- Colors -->
            <div class="flex gap-1 border-r border-gray-300 pr-2">
                <!-- Text Color -->
                <div class="relative">
                    <input type="color" @change="execCommand('foreColor', $event.target.value)"
                        class="w-9 h-9 rounded cursor-pointer border-0 p-1 hover:bg-gray-200" title="Text Color">
                </div>

                <!-- Background Color -->
                <div class="relative">
                    <input type="color" @change="execCommand('backColor', $event.target.value)"
                        class="w-9 h-9 rounded cursor-pointer border-0 p-1 hover:bg-gray-200"
                        title="Background Color">
                </div>
            </div>

            <!-- Clear Formatting -->
            <div class="flex gap-1">
                <button type="button" @click="clearFormatting()"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Clear Formatting">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
                <!-- Undo -->
                <button type="button" @click="execCommand('undo')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Undo (Ctrl+Z)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                </button>

                <!-- Redo -->
                <button type="button" @click="execCommand('redo')"
                    class="p-2 rounded hover:bg-gray-200 transition-colors duration-150" title="Redo (Ctrl+Y)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Editor -->
    <div x-ref="editor" contenteditable="true" @input="updateContent()"
        class="w-full min-h-[300px] p-4 border border-t-0 border-gray-300 rounded-b-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent overflow-auto prose max-w-none"
        :placeholder="$placeholder" style="outline: none;"></div>

    <!-- Hidden Input -->
    <input type="hidden" :name="$name" x-model="content">

    <!-- Helper Text -->
    @if ($helperText)
        <p class="mt-2 text-sm text-gray-500">{{ $helperText }}</p>
    @endif

    <!-- Link Dialog -->
    <div x-show="showLinkDialog" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        @click.away="showLinkDialog = false" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-96" @click.stop>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Insert Link</h3>
            <input type="url" x-model="linkUrl" placeholder="https://example.com"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @keydown.enter="insertLink()">
            <div class="flex gap-2 mt-4 justify-end">
                <button type="button" @click="showLinkDialog = false; linkUrl = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-150">
                    Cancel
                </button>
                <button type="button" @click="insertLink()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-150">
                    Insert
                </button>
            </div>
        </div>
    </div>

    <!-- Error Message Slot -->
    {{ $slot }}
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    [contenteditable]:empty:before {
        content: attr(placeholder);
        color: #9CA3AF;
        pointer-events: none;
        display: block;
    }

    [contenteditable] {
        background-color: white;
    }

    [contenteditable]:focus {
        background-color: #FEFEFE;
    }

    /* Prose styling for editor content */
    .prose h1 {
        font-size: 2em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose h2 {
        font-size: 1.5em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose h3 {
        font-size: 1.25em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose h4 {
        font-size: 1.1em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose h5 {
        font-size: 1em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose h6 {
        font-size: 0.9em;
        font-weight: bold;
        margin: 0.5em 0;
    }

    .prose p {
        margin: 0.5em 0;
    }

    .prose ul,
    .prose ol {
        margin: 0.5em 0;
        padding-left: 2em;
    }

    .prose li {
        margin: 0.25em 0;
    }

    .prose a {
        color: #3B82F6;
        text-decoration: underline;
    }

    .prose img {
        max-width: 100%;
        height: auto;
        margin: 1em 0;
    }

    .prose hr {
        border: 0;
        border-top: 2px solid #E5E7EB;
        margin: 1em 0;
    }
</style>

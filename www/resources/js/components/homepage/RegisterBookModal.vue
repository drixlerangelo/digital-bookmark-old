<template>
    <modal title="Register a new book" @modal-close="modalClose" @modal-proceed="registerBook" :action-disabled="validationFailed">
        <div class="new-book-info">
            <div class="book-cover-holder">
                <div class="book-cover-text">Cover Photo</div>
                <div class="book-cover-preview" ref="bookPreview">
                    <span class="btn-reset-book-cover" @click="resetBookCover">x</span>
                    <img class="img-book-preview" ref="imageBookPreview">
                </div>
                <div class="book-cover-selector" v-on="dragFileHandlers" ref="bookCoverSelector">
                    <strong class="input-file-mirror" @click="emulateInputFile">Choose a file</strong> or drag it here
                </div>
            </div>
            <div class="book-details">
                Title
                <textfield
                    :rounded="true"
                    :small="true"
                    placeholder="Book Title"
                    tooltip="This field should not be empty"
                    :use-callout="true"
                    :error-message="errors.title"
                    :validation-step="validateBookParamString('title')"
                    :error-font-size="12"
                ></textfield>
                Author
                <textfield
                    :rounded="true"
                    :small="true"
                    placeholder="Book Author"
                    tooltip="This field should not be empty"
                    :use-callout="true"
                    :error-message="errors.author"
                    :validation-step="validateBookParamString('author')"
                    :error-font-size="12"
                ></textfield>
                <div class="book-numbers">
                    <span width="50%" class="book-page-num">
                        Pages
                        <textfield
                            type="number"
                            placeholder="Pages"
                            tooltip="This field should not be less than one (1)"
                            :rounded="true"
                            :small="true"
                            :use-callout="true"
                            :error-message="errors.numPages"
                            :validation-step="validateBookParamNumber('numPages')"
                            :error-font-size="12"
                            :is-integer-only="true"
                        ></textfield>
                    </span>
                    <span width="50%" class="book-word-num">
                        Word Count
                        <textfield
                            type="number"
                            placeholder="Words"
                            tooltip="This field should not be less than one (1)"
                            :rounded="true"
                            :small="true"
                            :use-callout="true"
                            :error-message="errors.numWords"
                            :validation-step="validateBookParamNumber('numWords')"
                            :error-font-size="12"
                            :is-integer-only="true"
                        ></textfield>
                    </span>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
    import Modal from '../parent/Modal';
    import TextField from '../parent/TextField';
    import Validator from '../../lib/validation';

    export default {
        name: "RegisterBookModal",

        components : {
            'textfield' : TextField,
            'modal'     : Modal
        },

        data() {
            return {
                validationFailed : false,
                errors           : { title : '', author : '', numPages : '', numWords : '', coverFile : '' },
                book             : { title : '', author : '', numPages : 0, numWords : 0, coverFile : undefined },
                inputFile        : undefined,
                dragFileHandlers : {
                    dragenter : this.handleDragFile,
                    dragover  : this.handleDragFile,
                    dragleave : this.handleDragFile,
                    dragend   : this.handleDragFile,
                    drop      : this.handleDragFile
                }
            };
        },

        props : {
            stage : { type : String }
        },

        methods : {
            /**
             * Informs that the model is to be closed
             */
            modalClose() {
                this.$emit('modal-close');
            },

            /**
             * Handle the file being dragged over the element
             *
             * @param {Object} event
             */
            handleDragFile(event) {
                // Prevent the file from being opened
                event.preventDefault();
                event.stopPropagation();

                if (['dragenter', 'dragover'].indexOf(event.type) !== -1) {
                    // When a file is above the element, add an effect
                    this.$refs.bookCoverSelector.classList.add('cover-on-drag');
                } else if (['dragleave', 'dragend', 'drop'].indexOf(event.type) !== -1) {
                    // Remove the effect when the file is not over the element
                    this.$refs.bookCoverSelector.classList.remove('cover-on-drag');

                    if (event.type === 'drop' && event.dataTransfer.files.length > 0) {
                        // Only get the first file from what is given
                        this.book.coverFile = event.dataTransfer.files[0];

                        // Verify and show the image
                        this.previewBookCover();
                    }
                }
            },

            /**
             * Imitate the input file and process the selected file
             *
             * @param {Object} event
             */
            emulateInputFile(event) {
                if (event.type === 'click') {
                    // Create the input file
                    this.inputFile = document.createElement('input');
                    this.inputFile.type = 'file';
                    this.$refs.bookPreview.appendChild(this.inputFile);

                    // Add a listener when a file is chosen
                    this.inputFile.addEventListener('change', this.emulateInputFile);

                    // Imitate the input file when clicked
                    this.inputFile.click();
                } else if (event.type === 'change' && this.inputFile.files.length > 0) {
                    // Assign the chosen  file
                    this.book.coverFile = this.inputFile.files[0];

                    // Remove the input file
                    this.inputFile.remove();
                    this.inputFile = undefined;

                    // Verify and show the image
                    this.previewBookCover();
                }
            },

            /**
             * Preview the selected image
             */
            previewBookCover() {
                let validationFailed = this.validateImage() === false;

                if (validationFailed) {
                    this.book.coverFile = undefined;
                    return;
                }

                const reader = new FileReader();
                reader.addEventListener('load', function () {
                    this.$refs.imageBookPreview.src = reader.result;
                    this.$refs.bookPreview.style.display = 'block';
                    this.$refs.bookCoverSelector.style.display = 'none';
                }.bind(this), false);
                reader.readAsDataURL(this.book.coverFile);
            },

            /**
             * Resets the selected file
             */
            resetBookCover() {
                // Reset the image
                this.$refs.imageBookPreview.src = '';
                this.book.coverFile = undefined;

                // Hide the image preview
                this.$refs.bookPreview.style.display = 'none';

                // Show file selector
                this.$refs.bookCoverSelector.style.display = 'block';
            },

            /**
             * Validates the string input
             *
             * @param {String} columnName
             *
             * @returns {Object}
             */
            validateBookParamString(columnName) {
                return function (inputValue) {
                    this.book[columnName] = inputValue;
                    Validator.setParams(`book ${columnName}`, this.book[columnName]);
                    Validator.validateLength(1, Infinity);

                    let {passed, message} = window.ErrorManager.getResult(`book ${columnName}`);
                    this.errors[columnName] = message;
                    this.runErrorCheck();

                    return passed;
                }.bind(this);
            },

            /**
             * Validates if the string input can be an integer
             *
             * @param {String} columnName
             *
             * @returns {Object}
             */
            validateBookParamNumber(columnName) {
                return function (inputValue) {
                    this.book[columnName] = inputValue;
                    Validator.setParams(`book ${columnName}`, this.book[columnName]);
                    Validator.validateLength(1, Infinity, 'number');

                    let {passed, message} = window.ErrorManager.getResult(`book ${columnName}`);
                    this.errors[columnName] = message;
                    this.runErrorCheck();

                    if (passed) {
                        this.book[columnName] = parseInt(inputValue);
                    }

                    return passed;
                }.bind(this);
            },

            /**
             * Checks if the selected file is an image
             *
             * @returns {Boolean} passed
             */
            validateImage() {
                Validator.setParams('book cover', this.book.coverFile);
                Validator.validateFileSize('1 B', '5 MB');
                Validator.validateMimeType(['image/gif', 'image/png', 'image/jpeg']);
                let {passed, message} = window.ErrorManager.getResult('book cover');
                this.errors.coverFile = message;
                this.runErrorCheck();

                return passed;
            },

            /**
             * Checks for errors in other fields
             */
            runErrorCheck() {
                this.validationFailed = false;

                for (let columnName in this.errors) {
                    let error = this.errors[columnName];

                    if (error.length > 0) {
                        this.validationFailed = true;
                        break;
                    }
                }
            },

            /**
             * Submit and process the created book
             */
            registerBook() {
                let payload = new FormData();

                payload.append('name', this.book.title);
                payload.append('author', this.book.author);
                payload.append('numPages', parseInt(this.book.numPages));
                payload.append('numWords', parseInt(this.book.numWords));
                payload.append('stage', this.stage);

                if (this.book.coverFile instanceof File) {
                    payload.append('coverFile', this.book.coverFile);
                }

                axios.post('/book/register', payload, {
                    headers : { 'Content-Type' : 'multipart/form-data' }
                }).then(function ({ data }) {
                    const newBook = data.data.book;
                    this.$emit('book-created', newBook);
                }.bind(this)
                ).catch(function ({ response }) {
                    const serverErrors = response.data.errors;

                    this.errors.title = (typeof serverErrors.name === 'object') ? serverErrors.name[0] : '';
                    this.errors.author = (typeof serverErrors.author === 'object') ? serverErrors.author[0] : '';
                    this.errors.numPages = (typeof serverErrors.numPages === 'object') ? serverErrors.numPages[0] : '';
                    this.errors.numWords = (typeof serverErrors.numWords === 'object') ? serverErrors.numWords[0] : '';
                    this.errors.coverFile = (typeof serverErrors.coverFile === 'object') ? serverErrors.coverFile[0] : '';
                    this.validationFailed = true;

                    // If there's an error regarding the cover photo then just alert the user
                    if (this.errors.coverFile.length > 0) {
                        alert(this.errors.coverFile);
                        this.resetBookCover();
                        this.validationFailed = false;
                        this.errors.coverFile = '';
                    }
                }.bind(this));
            }
        }
    }
</script>

<style scoped>
    .new-book-info {
        padding-top: 1em;
        margin-left: 0.8em;
        margin-right: 0.8em;
        display: flex;
        height: 190px;
    }

    .book-cover-holder {
        display: block;
        width: 190px;
        margin: 0px 10px;
    }

    .book-cover-preview {
        display: none;
    }

    .img-book-preview {
        max-height: 125px;
    }

    .input-file-mirror:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .btn-reset-book-cover {
        cursor: pointer;
        font-size: 16px;
        position: fixed;
        margin: 0px 80px;
        color: #DE3121;
        font-weight: bold;
    }

    .btn-reset-book-cover:hover {
        color: #BF2C1F;
    }

    .book-cover-text {
        text-align: center;
    }

    .book-numbers, .book-details, .book-cover-text {
        font-size: 12px;
    }

    .book-cover-selector {
        margin-top: 0.5em;
        padding: 45px 10px;
        background: #C4C4C4;
        background-size: cover;
        font-size: 10px;
        text-align: center;
        border: 1px dashed #636363;
    }

    .book-details, .book-cover-text {
        margin-top: 0.5em;
    }

    .book-numbers {
        display: flex;
    }

    .book-page-num {
        margin-right: 0.5em;
    }

    .book-word-num {
        margin-left: 0.5em;
    }

    .cover-on-drag {
        background: #636363;
        border: 1px dashed #C4C4C4;
    }
</style>

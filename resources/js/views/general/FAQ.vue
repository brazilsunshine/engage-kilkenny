<template>
    <div class="container mt5 w-40-em pb10">
        <p class="title is-3 mb1">This is the FAQ section</p>

        <p>Choose a category:</p>

        <select class="input capitalize mb1" v-model="category" @change="categoryChanged">
            <option v-for="category in categories">
                {{ category }}
            </option>
        </select>

        <p>Every category has its own list of items:</p>

        <select class="input capitalize mb1" v-model="selectedItem">
            <option v-for="feature in litterkeys[this.category]" :value="feature.key">
                {{ $t('litter.' + category + '.' + feature.key) }}
            </option>
        </select>

        <div>
            <img
                :src="getBuildingsObject.image"
                class="faq-image"
            />

            <p>{{ getBuildingsObject.text }}</p>
        </div>

    </div>
</template>

<script>
import { categories } from "../../extra/categories";
import { litterkeys } from "../../extra/litterkeys";

export default {
    name: "FAQ",
    data () {
        return {
            categories: categories,
            category: 'buildings',
            litterkeys: litterkeys,
            selectedItem: 'derelict'
        };
    },
    computed: {
        /**
         * Get the correct text, depending on what buildings key was selected
         */
        getBuildingsObject () {

            let myArray = this.litterkeys[this.category];

            console.log("My Array", myArray);

            console.log("selectedItem", this.selectedItem);

            let myObject = myArray.find(obj => obj.key === this.selectedItem);

            console.log("My Object", myObject);

            if (myObject) {
                return myObject;
            }

            return false;
        }
    },
    methods: {
        /**
         * When we change the category,
         *
         * Update the selectedItem as the first element from the category array
         */
        categoryChanged () {
            this.selectedItem = this.litterkeys[this.category][0].key;
        }
    }
}
</script>

<style scoped>

    .w-40-em {
        width: 40em;
    }

</style>

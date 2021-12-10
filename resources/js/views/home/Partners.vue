<template>
    <div class="has-text-centered flex" style="align-items: center;">
        <a href="https://www.heritagecouncil.ie/" target="_blank" class="flex-1">
            <img
                loading="lazy"
                src="/assets/HC_logo_retina.png"
                alt="The Heritage Council"
                style="max-width: 300%;"
            >
        </a>

        <a href="https://www.heritagecouncil.ie/" target="_blank" class="flex-1">
            <img
                loading="lazy"
                src="/assets/ctchc_logo.png"
                alt="The Collaborative Town Centre Health Check"
                style="max-width: 50%;"
            >
        </a>
    </div>
</template>

<script>
export default {
    name: 'Partners'
};
</script>

<style scoped lang="scss">

    .partners-list {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        grid-gap: 32px 32px;
    }

    .partner {
        max-width: 200px;
        margin: auto;
    }

    .partner,
    .partner img {
        width: 100%;
    }

    /* Smaller screens */
    @media (max-width: 1024px) {

        .partners-list {
            grid-gap: 16px 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Mobile view */
    @media (max-width: 768px) {

    }

    /* Extra small */
    @media (max-width: 576px) {

        .partners-list {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }
    }

    /* Extra extra small */
    @media (max-width: 430px) {
    }

</style>

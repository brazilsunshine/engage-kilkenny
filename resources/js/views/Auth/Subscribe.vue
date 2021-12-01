<template>
    <div>
        <section class="hero is-info is-medium">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Want to help?
                    </h1>
                    <h2 class="subtitle">
                        Create an account and help us collect data about our historic town center.
                    </h2>
                </div>
            </div>
        </section>

        <section>
            <create-account />
        </section>
    </div>
</template>

<script>
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import CreateAccount from '../../components/CreateAccount'

export default {
    name: 'Subscribe',
    components: {
        CreateAccount,
        Loading
    },
    data () {
        return {
            loading: true
        };
    }
}
</script>

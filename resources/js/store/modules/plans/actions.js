export const actions = {
    /**
     * Someone wants to create a new account!
     */
    async CREATE_ACCOUNT (context, payload)
    {
        await axios.post('/register', {
            name: payload.name,
            username: payload.username,
            email: payload.email,
            password: payload.password,
            password_confirmation: payload.password_confirmation,
            "g-recaptcha-response": payload.g_recaptcha_response
        })
        .then(response => {
            console.log('create_account', response); // user_id, email

            // check response
            if (response.data.success)
            {
                alert('Congratulations! Your free account has been created. Please verify your email to activate login');
            }

            // Clear errors
            context.commit('createAccountErrors', []);

            // Todo - log the user in
        })
        .catch(error => {
            console.log('error.create_account', error);

            // populate errors
            context.commit('createAccountErrors', error.response.data.errors);
        });
    },

    /**
     * Get all of the available plans
     */
    async GET_PLANS (context)
    {
        await axios.get('/plans')
            .then(response => {
                console.log('get_plans', response);

                context.commit('setPlans', response.data);
            })
            .catch(error => {
                console.log('error.get_plans', error);
            });
    }

};

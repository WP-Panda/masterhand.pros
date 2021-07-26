$(function () {
    if ($(".two-checkout__form").length > 0) {
        // pull in the public encryption key for our environment
        TCO.loadPubKey('0DF368D1-A868-4647-AC3C-0437F694CB76');
    }

    $(".two-checkout__form").submit(function (e) {
        tokenRequest();
        return false;
    });

    let tokenRequest = function () {
        // setup token request arguments
        let expire = $(".two-checkout__expire").val();
        expire = expire.split('/');

        let expireMonth = expire[0];
        let expireYear = expire[1];

        let args = {
            sellerId: "250425983793",
            publishableKey: "0DF368D1-A868-4647-AC3C-0437F694CB76",
            ccNo: $(".two-checkout__card-number").val(),
            cvv: $(".two-checkout__cvv").val(),
            expMonth: expireMonth,
            expYear: expireYear,
        };

        // make the token request
        TCO.requestToken(
            // called when token created successfully
            function (data) {
                // set the token as the value for the token input
                $('.two-checkout__token').val(data.response.token.token);
                $('.two-checkout__form')[0].submit();
            },

            // called when token creation fails
            function (data) {
                console.log(data);
                if (data.errorCode === 200) {
                    tokenRequest();
                } else {
                    alert(data.errorMsg);
                }
            },

            args
        );
    };
});
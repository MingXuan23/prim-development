

function copyReferralLink(url){
    $.ajax({
    method: 'GET',
    url: url,
    success: function(data) {
        var currentURL = window.location.href;
        
        var urlWithoutParams = currentURL.split('?')[0];
        copyToClipboard(urlWithoutParams+'?referral_code='+data.referral_code,true);
        

    },
    error: function (data) {
        var currentURL = window.location.href;

        var urlWithoutParams = currentURL.split('?')[0];
        copyToClipboard(urlWithoutParams,false);
        console.log(data);
    }
});
}


function copyToClipboard(text,isReferralCode) {
// Create a temporary input element
var input = document.createElement('input');

// Set its value to the text that needs to be copied
input.style.position = 'absolute';
input.style.left = '-9999px';
input.value = text;


// Append it to the body
document.body.appendChild(input);

// Select the text inside the input element
input.select();

// Execute the copy command
document.execCommand('copy');

// Remove the input element from the DOM
document.body.removeChild(input);
var message = "<br><div class='alert alert-success'>Url copied, login to get the referral code</div>";
if(isReferralCode){
  message = "<br><div class='alert alert-success'>Url with your referral code copied</div>";
}
$('div.url-message').empty()
$('div.url-message').show()
$('div.url-message').append(message)
$('div.url-message').delay(3000).fadeOut()

// Set a timeout to hide the alert after 3 seconds


}
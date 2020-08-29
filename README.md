# mail-sender-php
Microservice to send email to website administrator.  

## Description  
Almost all of the portfolio websites on the web have a contact form, enabling the user to connect with the website owners. In most cases, this is the only part of the website that requires a backend. This application removes that dependency to build backend logic for each contact form by creating a SASS Microservice that can handle the requests from multiple websites. It exposes a single REST endpoint which receives the data and sends an email. It uses Firebase SDK for integration with external services to provide the required functionality. Each request coming to the service should have a valid captcha response string which is firstly verified to see if the request is valid. The service, then queries the Firestore to retrieve the template of the email and the address to which the email is to be sent. It will then apply the form data to the email template and will send the mail to the configured address.

## Database
The service uses Firestore provide by Google Firebase for storing the list of websites registered with it.  
`Collection` : `websites`  
Document structure  
`id` : `Website Url`  
Field | Type  | Description | Example
------|-------|-------------|--------
email| String | Email Address of the mail receiver |
mail-template | String | Email body template | Hi. ${name} with ${email} has asked a question regarding ${subject} - ${comments}

## Request structure  
Each request should have the following structure.
Field | Type  | Description |
------|-------|-------------|
site| String | Site url configured in firebase |
captcha | String | Captcha string |
formData | Object | Form data in key/value pairs. The service substitues matching keys in mail template with the values |

Note: If the formData has key subject, then the mail will have that value as the email subject else it will use the default subject `Contact form response`. If the formData has key email, then the mail will have that as the from address else it will use `EMAIL_FROM_ADDRESS` environment variable value as the from address.

## Environment Variables
The service requires a number of config data to be present as environment variables. These include the service account.

Variable | Description |
------|-------------|
FIREBASE_CREDENTIALS | Service account JSON of App created in Firestore |
RECAPTCHA_SITE_KEY | ReCaptcha server key |
EMAIL_FROM_ADDRESS | Default from address to be set in the emails sent |
EMAIL_FROM_ADDRESS | Name to be sent in header of email |

## API
### Send email

Sends email to the configured user

* **URL**

  /send-email

* **Method:**

  `POST`
  
* **Data Params**

  **Required:**
 
   `site=[string]`  
   `formData=[object]`  
   `captcha=[string]`

* **Success Response:**

  * **Code:** 200 <br />
 
* **Error Response:**
  * **Code:** 400 Bad Request <br />
    **Content:** `{ error-code: 100, "error-message" : "Captcha Verification failed" }`
  
  OR
  
  * **Code:** 400 Bad Request <br />
    **Content:** `{ error-code: 101, "error-message" : "Website url is not registered with the service" }`
    
  OR
  
  * **Code:** 400 Bad Request <br />
    **Content:** `{ error-code: 102, "error-message" : "Website url is not present in request" }`

  OR

  * **Code:** 500 Internal Server Error <br />
    **Content:** `{ error-code: 500, "error-message" : "An internal error occurred" }`

* **Sample Call:**

  ```
  curl --location --request POST 'localhost:8080' --header 'Content-Type: application/json' --data-raw '{
    "captcha": assssssssssasA12121Sdasdadr3232eqdda,
    "site": "mysite.com",
    "formData": {
        "name": "Jijo",
        "subject": "Hello",
        "comments" : "Lorem ipsum dolor sit amet, consectetur adipiscing elit"
    }
  }'
  ```
   

### Tech stack
* PHP

### External Integrations
* Google Firebase
* Google ReCaptcha

### License
[MIT](https://github.com/jijojames18/mail-sender-php/blob/master/LICENSE)

### Reference
* [Google Firebase](https://firebase.google.com/docs/admin/setup#java)
* [Firebase Admin Console](https://console.firebase.google.com/)
* [Google ReCaptcha](https://developers.google.com/recaptcha/docs/verify)

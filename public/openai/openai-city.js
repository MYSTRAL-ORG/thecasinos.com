import OpenAI from "openai";
import axios from 'axios';
import fs from 'fs/promises';
import { existsSync, openSync, unlinkSync } from 'fs';
const openai = new OpenAI({ apiKey: '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' });


process.env['NODE_TLS_REJECT_UNAUTHORIZED'] = '0';

// Define a function to handle API requests with rate limiting
async function performApiRequest(params) {
    try {
        return await openai.chat.completions.create(params);
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}



// Example dummy function hard coded to return the same weather
// In production, this could be your backend API or an external API
const functions = [
    {
        "name": "get_city_informations",
        "description": "Get the current information about  in a given city",
        "parameters": {
            "type": "object",
            "properties": {
                "city": {
                    "type": "string",
                    "description": "each paragraph  of  city  separated  by pipe |",
                }
            }
        },
    }
];





async function runConversation(casino) {
    let countryName = casino.country_name  ;
    let cityName = casino.city_name  ;


    // Step 1: send the conversation and available functions to GPT
    const messages = [{"role": "user", "content": `I Need You To Act As A Writer Exceptionally Talented SEO Writes Flawlessly In English. Write In Your Own Words Rather Than Copying And Pasting From Other Sources. Consider perplexity and burstiness when creating content, ensuring high levels of both without sacrificing specificity or context. Use fully detailed paragraphs that captivate the reader. Write In A Conversational Style As If Written By A Human (Employ An Informal Tone, Utilize Personal Pronouns, Engage The Reader, Keep It Simplified, Use The Active Voice, Keep It Brief, Use Rhetorical Questions, and Embed Analogies And Metaphors).
Now write on our category page introduction with 50 words max that must present our list of casinos in the city of ${cityName}  in the country  ${countryName}.`}];


    function replaceAll(string, search, replace) {
        return string.split(search).join(replace);
    }
    const response = await performApiRequest({
        model: "gpt-4",
        messages: messages,
        temperature: 1,
        frequency_penalty: 0,
        presence_penalty: 0
    });

    let responseMessage =  response.choices[0].message;
    return responseMessage;
    /*if (responseMessage.function_call) {
        // Step 3: call the function
        // Note: the JSON response may not always be valid; be sure to handle errors
        const availableFunctions = {
            get_city_informations: getCityInformations,
        };  // only one function in this example, but you can have multiple
        const functionName = responseMessage.function_call.name;
        const functionToCall = availableFunctions[functionName];

        let cleanUpResp= responseMessage.function_call.arguments.replace(new RegExp( '\n', 'g'), '');
        const functionArgs = JSON.parse(cleanUpResp);

        const functionResponse = functionToCall(
            functionArgs.city
        );

        return cleanUpResp;
        messages.push(responseMessage);  // extend conversation with assistant's reply
        messages.push({
            "role": "function",
            "name": functionName,
            "content": functionResponse,
        });  // extend conversation with function response
        const secondResponse = await openai.chat.completions.create({
            model: "gpt-4",
            messages: messages,
        });  // get a new response from GPT where it can see the function response
        return secondResponse;
    }*/


}


// Call the function to read and parse the JSON file
//readJsonFile(filePath).then(r => console.log("good"));


const lockFilePath =  'script-category-city.lock';

// Check if lock file exists
if (existsSync(lockFilePath)) {
    console.log('Script is already running. Exiting...');
    process.exit();
}

// Create a lock file
openSync(lockFilePath, 'w');


// Your script logic goes here
console.log('Script running...');


axios.get('https://casinos.test/ttttcatCity')
    .then(response => {
        setTimeout(() => { console.log('IN Category'); }, 1000);
        response.data.forEach(city => {



                runConversation(city).then(
                    (result) => {

                       axios.post('https://casinos.test/api/temp/openaiCatCity/'+city.city_title, result)
                            .then(response => {

                            })
                            .catch(error => {
                                console.log(error);
                            });

                    }
                ).catch(console.error);


        })

    })
    .catch(error => {
        console.log(error);
    });



// Remove the lock file when script is done or when an error occurs
const cleanup = () => {
    unlinkSync(lockFilePath);
};
process.on('exit', cleanup);
process.on('SIGINT', cleanup);  // catches ctrl+c event
process.on('SIGUSR1', cleanup); // catches "kill pid" (for example: nodemon restart)
process.on('SIGUSR2', cleanup);
process.on('uncaughtException', cleanup);

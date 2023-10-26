import OpenAI from "openai";
import axios from 'axios';
import fs from 'fs/promises';
import { existsSync, openSync, unlinkSync } from 'fs';
const openai = new OpenAI({ apiKey: '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' });




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
function getCasinoInformations(title, novel,casino_sumup, casino_games, casino_fun_facts, casino_resume_1_line, casino_resume_2_words) {
    const casinoInfo = {
        "title": title,
        "novel": novel,
        "casino_sumup": casino_sumup,
        "casino_games": casino_games,
        "casino_fun_facts": casino_fun_facts,
        "casino_resume_1_line": casino_resume_1_line,
        "casino_resume_2_words": casino_resume_2_words

    };
    return JSON.stringify(casinoInfo);
}


function getListFaetures(casino)
{
    let gameListString = [];

    casino.cat_poker && gameListString.push("Poker");
    casino.cat_sportsbook && gameListString.push("Sportsbook");
    casino.cat_horseracing && gameListString.push("Horse racing");
    casino.cat_simulcasting && gameListString.push("Simulcast");
    casino.cat_offtrack && gameListString.push("Offtrack");
    casino.cat_greyhounds && gameListString.push("Greyhounds");
    casino.cat_bingo && gameListString.push("Bingo");
    casino.cat_slotmachines && gameListString.push("Slot Machines");
    casino.cat_tablegames && gameListString.push("Table games");

    return gameListString.join(",");
}


async function runConversation(casino) {
    let casinoInfo = casino.name;
    let casinoCity = casino.city_name  || casino.state_name  || casino.country_name ;

    if (casinoCity != null) {
        casinoInfo = casinoInfo + " , " + casinoCity;
    }


    const listFeatures = getListFaetures(casino);

    const open  =  ( casino.opened != null) ? '- Casino opened: '+ casino.opened : '';
    const alwaysOpen  =    ( casino.always_open!= null) ? " '– Always opened:  yes" : ''
    const pokerRoomName = ( casino.poker_room_name!= null) ? " '– Poker Room Name :"+casino.poker_room_name : '';
    const pokerTables = ( casino.poker_tables!= null) ? " '– Poker Tables :"+casino.poker_tables : '';
    const table_games = ( casino.table_games!= null) ? " '– Table Games :"+casino.table_games : '';
    const gamingMachines = ( casino.gaming_machines!= null) ? " '– Gaming Machines :"+casino.gaming_machines : '';
    const casinoSquare = ( casino.square_footage!= null) ? " '– Casino Square :"+casino.square_footage : '';
    const hotelName = ( casino.hotel_name!= null) ? " '– Hotel Name :"+casino.hotel_name : '';
    const owners = ( casino.owners!= null) ? " '– Owners :"+casino.owners : '';

    // Step 1: send the conversation and available functions to GPT
    const messages = [{"role": "user", "content": `
    I Need You To Act As A Writer Exceptionally Talented SEO Writes Flawlessly In English. Write In Your Own Words Rather Than Copying And Pasting From Other Sources. Consider perplexity and burstiness when creating content, ensuring high levels of both without sacrificing specificity or context. Use fully detailed paragraphs that captivate the reader. Write In A Conversational Style As If Written By A Human (Employ An Informal Tone, Utilize Personal Pronouns, Engage The Reader, Keep It Simplified, Use The Active Voice, Keep It Brief, Use Rhetorical Questions, and Embed Analogies And Metaphors).

   Now write a positive 1000 words minimum description (not less than 1000 words) from today which describes the different aspects of the casino : ${casinoInfo} giving all the informations below about the casino in the description, here the main datas of the casino that will help you write your text :

                ${open}
                ${alwaysOpen}
                ${pokerRoomName}
                ${pokerTables}
                ${table_games}
                ${gamingMachines}
                ${casinoSquare}
                ${hotelName}
                ${owners}
            – Game categories: ${listFeatures}

            Choose a short original title for the description.
            Following the novel I would like you to create 4 paragraphs (a summary of 100 words maximum) presenting this casino:

            – $Casino_Sumup
            – $Casino_Games
            - $Casino_Fun_Facts

            To finish I would like a short one-line summary (20 words maximum) of the casino and a 2-word summary (exemple : Elegance & )
            – $Casino_Resume_1_line
            – $Casino_Resume_2_words


            `}];

    const functions = [
        {
            "name": "get_casino_informations",
            "description": "Get the current information about  in a given casino",
            "parameters": {
                "type": "object",
                "properties": {
                    "title": {
                        "type": "string",
                        "description": "The title of the novel",
                    },
                    "novel": {
                        "type": "string",
                        "description": "each paragraph  of  the huge description of the novel separated  by pipe |",
                    },
                    "casino_sumup": {
                        "type": "string",
                        "description": "This is the casino_sumup  of casino",
                    },
                    "casino_games": {
                        "type": "string",
                        "description": "This is the casino_games  of casino",
                    },
                    "casino_fun_facts": {
                        "type": "string",
                        "description": "This is the casino_fun_facts  of casino",
                    },
                    "casino_resume_1_line": {
                        "type": "string",
                        "description": "This is the casino_resume_1_line  of casino",
                    },
                    "casino_resume_2_words": {
                        "type": "string",
                        "description": "This is the casino_resume_2_words  of casino",
                    },


                }
            },
        }
    ];
    function replaceAll(string, search, replace) {
        return string.split(search).join(replace);
    }
    const response = await performApiRequest({
        model: "gpt-4",
        messages: messages,
        functions: functions,
        function_call: "auto",  // auto is default, but we'll be explicit
        temperature: 1,
        frequency_penalty: 0,
        presence_penalty: 0
    });

    let responseMessage = response.choices[0].message;



    if (responseMessage.function_call) {
        // Step 3: call the function
        // Note: the JSON response may not always be valid; be sure to handle errors
        const availableFunctions = {
            get_casino_informations: getCasinoInformations,
        };  // only one function in this example, but you can have multiple
        const functionName = responseMessage.function_call.name;
        const functionToCall = availableFunctions[functionName];

        let cleanUpResp= responseMessage.function_call.arguments.replace(new RegExp( '\n', 'g'), '');
        const functionArgs = JSON.parse(cleanUpResp);

        const functionResponse = functionToCall(
            functionArgs.title,
            functionArgs.novel,
            functionArgs.casino_sumup,
            functionArgs.casino_games,
            functionArgs.casino_fun_facts,
            functionArgs.casino_resume_1_line,
            functionArgs.casino_resume_2_words,
        );
        console.log("Response")

        return functionResponse;
        /*// Step 4: send the info on the function call and function response to GPT
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
        return secondResponse;*/
    }
}


// Call the function to read and parse the JSON file
//readJsonFile(filePath).then(r => console.log("good"));


const lockFilePath =  'script.lock';

// Check if lock file exists
if (existsSync(lockFilePath)) {
    console.log('Script is already running. Exiting...');
    process.exit();
}

// Create a lock file
openSync(lockFilePath, 'w');


// Your script logic goes here
console.log('Script running...');


axios.get('http://casinos.test/tttt')
    .then(response => {
        setTimeout(() => { console.log('World!'); }, 1000);
        response.data.forEach(casino => {


            console.log('http://casinos.test/api/openai/'+casino.id);

                runConversation(casino).then(
                    (result) => {
                        axios.post('http://casinos.test/api/openai/'+casino.id, result)
                            .then(response => {
                                console.log(response.data);
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

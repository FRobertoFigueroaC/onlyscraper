const express = require( 'express' );
const scrapeProfile = require( './scraper' );

const app = express();
const PORT = 3000;

app.get( '/scrape/:username', async ( req, res ) => {
    const { username } = req.params;

    console.log('Scraping profile:', username);

    try {
        const data = await scrapeProfile( username );
        res.status( 200 ).json( data );
    } catch ( err ) {
        res.status( 500 ).json( { error: err.message } );
    }
} );

app.listen( PORT, () => {
    console.log( `🚀 Scraper API corriendo en http://localhost:${ PORT }` );
} );

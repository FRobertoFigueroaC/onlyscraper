const { chromium } = require( 'playwright' );

async function fetchOnlyFansProfileHtml( username ) {
    const browser = await chromium.launch( { headless: true } );
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        await page.goto( `https://onlyfans.com/${ username }`, { waitUntil: 'domcontentloaded' } );
        await page.waitForTimeout( 4000 ); // loading js content
        const bodyHtml = await page.content(); // Get HTML

        await browser.close();

        return {
            html: bodyHtml,
        };
    } catch ( err ) {
        await browser.close();
        throw new Error( `Scraping error: ${ err.message }` );
    }
}

// CLI
if ( require.main === module ) {
    const username = process.argv[ 2 ];
    if ( !username ) {
        console.error( 'Add a username' );
        process.exit( 1 );
    }

    fetchOnlyFansProfileHtml( username )
        .then( data => {
            console.log( data.html );
        } )
        .catch( err => console.error( '❌ Error:', err.message ) );
}

module.exports = fetchOnlyFansProfileHtml;

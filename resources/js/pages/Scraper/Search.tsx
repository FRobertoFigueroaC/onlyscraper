import { useEffect, useState, FormEvent } from 'react';
import { useForm } from '@inertiajs/react';
import axios from 'axios';

type Profile = {
    username: string;
    name: string | null;
    bio: string | null;
    likes: number;
};

export default function Search() {
    const [ tab, setTab ] = useState<'search' | 'scrape'>( 'search' );

    const [ results, setResults ] = useState<Profile[]>( [] );
    const { data, setData } = useForm( { query: '', scrapeUser: '' } );
    const [ scrapeResult, setScrapeResult ] = useState<Profile | null>( null );
    const [ scrapeLoading, setScrapeLoading ] = useState( false );

    const search = async ( e: FormEvent ) => {
        e.preventDefault();

        try {
            const res = await axios.get( '/search', {
                params: { q: data.query },
            } );
            setResults( res.data.results );
        } catch ( error ) {
            console.error( 'Search error:', error );
        }
    };

    const scrape = async ( e: FormEvent ) => {
        e.preventDefault();
        setScrapeResult( null );
        setScrapeLoading( true );

        try {
            const res = await axios.get( `/scrape/${ data.scrapeUser }` );
            setScrapeResult( res.data );
        } catch ( error ) {
            console.error( 'Scrape error:', error );
        } finally {
            setScrapeLoading( false );
        }
    };

    return (
        <div className="max-w-2xl mx-auto py-10 px-4">
            <div className="flex border-b mb-6">
                <button
                    className={ `px-4 py-2 font-semibold ${ tab === 'search' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'
                        }` }
                    onClick={ () => setTab( 'search' ) }
                >
                    Search
                </button>
                <button
                    className={ `px-4 py-2 font-semibold ${ tab === 'scrape' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'
                        }` }
                    onClick={ () => setTab( 'scrape' ) }
                >
                    Scrap
                </button>
            </div>

            { tab === 'search' && (
                <>
                    <h1 className="text-3xl font-bold mb-6 text-center">
                        Use Scout to find OnlyFans profiles
                    </h1>

                    <form onSubmit={ search } className="mb-8">
                        <input
                            type="text"
                            value={ data.query }
                            onChange={ ( e ) => setData( 'query', e.target.value ) }
                            className="w-full border px-4 py-2 rounded shadow"
                            placeholder="Find by name, username or bio"
                        />
                        <button
                            type="submit"
                            className="mt-4 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition"
                        >
                            Search
                        </button>
                    </form>

                    { results.length > 0 && (
                        <div className="bg-white shadow-md rounded p-4 text-gray-700">
                            <h2 className="text-xl font-semibold mb-2">Results:</h2>
                            <ul className="space-y-4">
                                { results.map( ( r ) => (
                                    <li key={ r.username } className="border-b pb-2">
                                        <p>
                                            <strong>Username:</strong>{ ' ' }
                                            <a
                                                href={ `https://onlyfans.com/${ r.username }` }
                                                className="text-blue-600 hover:underline"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                { r.username }
                                            </a>
                                        </p>
                                        <p><strong>Name:</strong> { r.name || 'N/A' }</p>
                                        <p><strong>Bio:</strong> { r.bio || 'N/A' }</p>
                                        <p><strong>Likes:</strong> { r.likes } ❤️</p>
                                    </li>
                                ) ) }
                            </ul>
                        </div>
                    ) }
                </>
            ) }

            { tab === 'scrape' && (
                <form onSubmit={ scrape } className="space-y-6">
                    <h1 className="text-2xl font-bold text-center">Scraping profile</h1>
                    <input
                        type="text"
                        value={ data.scrapeUser }
                        onChange={ ( e ) => setData( 'scrapeUser', e.target.value ) }
                        className="w-full border px-4 py-2 rounded shadow"
                        placeholder="Enter username"
                    />
                    <button
                        type="submit"
                        className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition"
                        disabled={ scrapeLoading }
                    >
                        { scrapeLoading ? 'Scraping...' : 'Scraping profile' }
                    </button>

                    { scrapeResult && (
                        <div className="bg-white shadow-md rounded p-4 text-gray-700">
                            <h2 className="text-xl font-semibold mb-2">Result:</h2>
                            <p>
                                <strong>Username:</strong> { scrapeResult.username }
                                <a href={ `https://onlyfans.com/${ scrapeResult.username }` }
                                    className="text-blue-600 hover:underline"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    { scrapeResult.username }
                                </a>
                            </p>
                            <p><strong>Name:</strong> { scrapeResult.name }</p>
                            <p><strong>Bio:</strong> { scrapeResult.bio }</p>
                            <p><strong>Likes:</strong> { scrapeResult.likes } ❤️</p>
                        </div>
                    ) }
                </form>
            ) }
        </div>
    );
}

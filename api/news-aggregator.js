/**
 * DISCLOSURE DAY - News Aggregator
 * Fetches live updates from multiple sources
 *
 * APIs Used:
 * - NewsAPI.org (free tier: 100 requests/day)
 * - Twitter/X API v2 (requires developer account)
 * - Reddit API (no key required for read-only)
 * - TMDB API (free with account)
 */

class DisclosureDayNews {
  constructor(config) {
    this.newsApiKey = config.newsApiKey || '';
    this.twitterBearerToken = config.twitterBearerToken || '';
    this.tmdbApiKey = config.tmdbApiKey || '';
    this.cacheTime = 5 * 60 * 1000; // 5 minutes
    this.cache = {};
  }

  // NewsAPI.org - Entertainment News
  async fetchNewsAPI() {
    if (!this.newsApiKey) {
      console.warn('NewsAPI key not configured');
      return [];
    }

    const cacheKey = 'newsapi';
    if (this.cache[cacheKey] && Date.now() - this.cache[cacheKey].time < this.cacheTime) {
      return this.cache[cacheKey].data;
    }

    try {
      const queries = [
        'Disclosure Day Spielberg',
        'Disclosure Day movie',
        'Emily Blunt UFO',
        'Spielberg 2026'
      ];

      const results = [];

      for (const query of queries) {
        const response = await fetch(
          `https://newsapi.org/v2/everything?q=${encodeURIComponent(query)}&sortBy=publishedAt&language=en&apiKey=${this.newsApiKey}`
        );
        const data = await response.json();

        if (data.articles) {
          results.push(...data.articles.slice(0, 5));
        }
      }

      // Deduplicate by title
      const unique = [...new Map(results.map(item => [item.title, item])).values()];

      this.cache[cacheKey] = { data: unique, time: Date.now() };
      return unique;
    } catch (error) {
      console.error('NewsAPI fetch failed:', error);
      return [];
    }
  }

  // Twitter/X API v2 - Social Mentions
  async fetchTwitter() {
    if (!this.twitterBearerToken) {
      console.warn('Twitter bearer token not configured');
      return [];
    }

    const cacheKey = 'twitter';
    if (this.cache[cacheKey] && Date.now() - this.cache[cacheKey].time < this.cacheTime) {
      return this.cache[cacheKey].data;
    }

    try {
      // Search for relevant hashtags and mentions
      const query = '#DisclosureDay OR #DisclosureDay2026 OR "Disclosure Day" Spielberg -is:retweet';

      const response = await fetch(
        `https://api.twitter.com/2/tweets/search/recent?query=${encodeURIComponent(query)}&max_results=20&tweet.fields=created_at,public_metrics,author_id`,
        {
          headers: {
            'Authorization': `Bearer ${this.twitterBearerToken}`
          }
        }
      );

      const data = await response.json();

      if (data.data) {
        this.cache[cacheKey] = { data: data.data, time: Date.now() };
        return data.data;
      }

      return [];
    } catch (error) {
      console.error('Twitter fetch failed:', error);
      return [];
    }
  }

  // Reddit - r/movies and related subreddits
  async fetchReddit() {
    const cacheKey = 'reddit';
    if (this.cache[cacheKey] && Date.now() - this.cache[cacheKey].time < this.cacheTime) {
      return this.cache[cacheKey].data;
    }

    try {
      const subreddits = ['movies', 'entertainment', 'boxoffice', 'Spielberg'];
      const results = [];

      for (const sub of subreddits) {
        const response = await fetch(
          `https://www.reddit.com/r/${sub}/search.json?q=disclosure+day+OR+spielberg+ufo&sort=new&limit=10`
        );
        const data = await response.json();

        if (data.data && data.data.children) {
          results.push(...data.data.children.map(post => ({
            title: post.data.title,
            url: `https://reddit.com${post.data.permalink}`,
            score: post.data.score,
            subreddit: post.data.subreddit,
            created: new Date(post.data.created_utc * 1000),
            comments: post.data.num_comments
          })));
        }
      }

      // Sort by score
      results.sort((a, b) => b.score - a.score);

      this.cache[cacheKey] = { data: results.slice(0, 15), time: Date.now() };
      return results.slice(0, 15);
    } catch (error) {
      console.error('Reddit fetch failed:', error);
      return [];
    }
  }

  // TMDB - Movie Database Info
  async fetchTMDB() {
    if (!this.tmdbApiKey) {
      console.warn('TMDB API key not configured');
      return null;
    }

    const cacheKey = 'tmdb';
    if (this.cache[cacheKey] && Date.now() - this.cache[cacheKey].time < this.cacheTime) {
      return this.cache[cacheKey].data;
    }

    try {
      // Search for the movie
      const searchResponse = await fetch(
        `https://api.themoviedb.org/3/search/movie?api_key=${this.tmdbApiKey}&query=Disclosure%20Day&year=2026`
      );
      const searchData = await searchResponse.json();

      if (searchData.results && searchData.results.length > 0) {
        const movieId = searchData.results[0].id;

        // Get full details
        const detailsResponse = await fetch(
          `https://api.themoviedb.org/3/movie/${movieId}?api_key=${this.tmdbApiKey}&append_to_response=credits,videos,images`
        );
        const details = await detailsResponse.json();

        this.cache[cacheKey] = { data: details, time: Date.now() };
        return details;
      }

      return null;
    } catch (error) {
      console.error('TMDB fetch failed:', error);
      return null;
    }
  }

  // Aggregate all sources
  async fetchAll() {
    const [news, tweets, reddit, tmdb] = await Promise.all([
      this.fetchNewsAPI(),
      this.fetchTwitter(),
      this.fetchReddit(),
      this.fetchTMDB()
    ]);

    return {
      news: this.formatNewsItems(news),
      social: this.formatTweets(tweets),
      reddit: reddit,
      movieData: tmdb,
      lastUpdated: new Date().toISOString()
    };
  }

  // Format news items for display
  formatNewsItems(articles) {
    return articles.map(article => ({
      source: this.getSourceAbbrev(article.source?.name),
      title: article.title,
      description: article.description,
      url: article.url,
      image: article.urlToImage,
      published: new Date(article.publishedAt),
      timeAgo: this.timeAgo(new Date(article.publishedAt))
    }));
  }

  // Format tweets for display
  formatTweets(tweets) {
    return tweets.map(tweet => ({
      source: 'X',
      text: tweet.text,
      metrics: tweet.public_metrics,
      created: new Date(tweet.created_at),
      timeAgo: this.timeAgo(new Date(tweet.created_at))
    }));
  }

  // Get source abbreviation
  getSourceAbbrev(source) {
    const abbrevs = {
      'Variety': 'VAR',
      'The Hollywood Reporter': 'THR',
      'Deadline': 'DL',
      'IndieWire': 'IW',
      'Entertainment Weekly': 'EW',
      'Collider': 'COL',
      'Screen Rant': 'SR',
      'IGN': 'IGN'
    };
    return abbrevs[source] || source?.substring(0, 3).toUpperCase() || 'NEWS';
  }

  // Calculate time ago
  timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);

    const intervals = {
      year: 31536000,
      month: 2592000,
      week: 604800,
      day: 86400,
      hour: 3600,
      minute: 60
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
      const interval = Math.floor(seconds / secondsInUnit);
      if (interval >= 1) {
        return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`;
      }
    }

    return 'Just now';
  }
}

// Render news to the feed
function renderNewsFeed(items, containerId = 'newsFeed') {
  const container = document.getElementById(containerId);
  if (!container) return;

  const html = items.map((item, index) => `
    <div class="dd-news-item ${index === 0 ? 'featured' : ''}">
      <div class="dd-news-source">${item.source}</div>
      <div class="dd-news-content">
        <h4><a href="${item.url}" target="_blank" rel="noopener">${item.title}</a></h4>
        <p>${item.description || ''}</p>
      </div>
      <div class="dd-news-time">${item.timeAgo}</div>
    </div>
  `).join('');

  container.innerHTML = html;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
  // Configure with your API keys
  const newsAggregator = new DisclosureDayNews({
    newsApiKey: '', // Get free key at newsapi.org
    twitterBearerToken: '', // Twitter Developer Portal
    tmdbApiKey: '' // Get free key at themoviedb.org
  });

  // Only fetch if keys are configured
  if (newsAggregator.newsApiKey) {
    try {
      const data = await newsAggregator.fetchAll();
      if (data.news.length > 0) {
        renderNewsFeed(data.news);
      }
      console.log('News data loaded:', data);
    } catch (error) {
      console.log('Using static news feed (API keys not configured)');
    }
  }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { DisclosureDayNews, renderNewsFeed };
}

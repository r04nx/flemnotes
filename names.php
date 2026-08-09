<?php
// Comprehensive list of cool names for route generation

class NameGenerator {
    
    // Mythical creatures and beings
    private static $mythical = [
        'phoenix', 'dragon', 'griffin', 'hydra', 'kraken', 'titan', 'atlas',
        'thor', 'loki', 'odin', 'zeus', 'hades', 'apollo', 'artemis', 'athena',
        'medusa', 'minotaur', 'sphinx', 'pegasus', 'unicorn', 'chimera', 'cerberus',
        'valkyrie', 'fenrir', 'yeti', 'basilisk', 'banshee', 'djinn', 'golem'
    ];
    
    // Fantasy elements
    private static $fantasy = [
        'wizard', 'mage', 'sage', 'knight', 'rogue', 'paladin', 'druid', 'ranger',
        'elf', 'dwarf', 'hobbit', 'orc', 'goblin', 'troll', 'fairy', 'pixie',
        'spell', 'rune', 'crystal', 'potion', 'wand', 'staff', 'sword', 'shield',
        'quest', 'realm', 'kingdom', 'castle', 'tower', 'dungeon', 'forge', 'temple'
    ];
    
    // Movie & Series characters/references
    private static $movies = [
        'stark', 'potter', 'frodo', 'gandalf', 'yoda', 'vader', 'neo', 'morpheus',
        'trinity', 'aragorn', 'legolas', 'gimli', 'luke', 'leia', 'han', 'chewie',
        'strange', 'tony', 'peter', 'bruce', 'thor', 'loki', 'wanda', 'vision',
        'eleven', 'hopper', 'dustin', 'mike', 'will', 'max', 'steve', 'nancy'
    ];
    
    // Cool places & locations
    private static $places = [
        'avalon', 'asgard', 'olympus', 'atlantis', 'eden', 'valhalla', 'elysium',
        'narnia', 'mordor', 'shire', 'rivendell', 'gondor', 'rohan', 'tatooine',
        'hoth', 'endor', 'naboo', 'coruscant', 'wakanda', 'asgard', 'xandar',
        'gotham', 'metropolis', 'arkham', 'central', 'star', 'midgard', 'jotunheim'
    ];
    
    // Elements & Nature
    private static $elements = [
        'fire', 'ice', 'storm', 'thunder', 'lightning', 'wind', 'earth', 'water',
        'shadow', 'light', 'dark', 'void', 'chaos', 'nova', 'solar', 'lunar',
        'star', 'comet', 'meteor', 'nebula', 'galaxy', 'cosmic', 'astral', 'celestial',
        'frost', 'flame', 'blaze', 'ember', 'spark', 'bolt', 'wave', 'tide'
    ];
    
    // Cool adjectives
    private static $adjectives = [
        'epic', 'mystic', 'cosmic', 'royal', 'wild', 'swift', 'brave', 'wise',
        'ancient', 'eternal', 'divine', 'sacred', 'hidden', 'secret', 'lost', 'forgotten',
        'golden', 'silver', 'crimson', 'azure', 'jade', 'obsidian', 'crystal', 'iron',
        'silent', 'loud', 'bright', 'dark', 'pure', 'chaos', 'order', 'prime'
    ];
    
    // Short cool words
    private static $short = [
        'ace', 'zen', 'fox', 'wolf', 'hawk', 'bear', 'lion', 'tiger', 'eagle',
        'nova', 'zeta', 'alpha', 'beta', 'omega', 'sigma', 'delta', 'gamma',
        'bolt', 'dash', 'rush', 'flash', 'blitz', 'storm', 'fury', 'rage',
        'echo', 'ghost', 'shade', 'mist', 'fog', 'dusk', 'dawn', 'noon'
    ];
    
    // Numbers/Codes (for variety)
    private static $numbers = [
        '7', '13', '42', '99', '101', '404', '777', '369', '21', '88'
    ];
    
    /**
     * Generate a random memorable route
     * @param int $parts Number of parts (2 or 3)
     * @return string Generated route
     */
    public static function generateRoute($parts = 2) {
        $allCategories = [
            self::$mythical,
            self::$fantasy,
            self::$movies,
            self::$places,
            self::$elements,
            self::$adjectives,
            self::$short
        ];
        
        $route = [];
        $usedCategories = [];
        
        for ($i = 0; $i < $parts; $i++) {
            // Pick a random category that hasn't been used
            do {
                $categoryIndex = array_rand($allCategories);
            } while (in_array($categoryIndex, $usedCategories) && count($usedCategories) < count($allCategories));
            
            $usedCategories[] = $categoryIndex;
            $category = $allCategories[$categoryIndex];
            $word = $category[array_rand($category)];
            $route[] = $word;
        }
        
        // Occasionally add a number for uniqueness
        if (rand(1, 5) === 1) {
            $route[] = self::$numbers[array_rand(self::$numbers)];
        }
        
        return implode('', $route);
    }
    
    /**
     * Generate multiple route options
     * @param int $count Number of routes to generate
     * @return array Array of generated routes
     */
    public static function generateMultiple($count = 5) {
        $routes = [];
        for ($i = 0; $i < $count; $i++) {
            $parts = rand(2, 3);
            $routes[] = self::generateRoute($parts);
        }
        return $routes;
    }
    
    /**
     * Get a specific themed route
     * @param string $theme Theme name
     * @return string Generated route
     */
    public static function getThemedRoute($theme = 'random') {
        switch ($theme) {
            case 'mythical':
                return self::$mythical[array_rand(self::$mythical)] . 
                       self::$elements[array_rand(self::$elements)];
            
            case 'fantasy':
                return self::$adjectives[array_rand(self::$adjectives)] . 
                       self::$fantasy[array_rand(self::$fantasy)];
            
            case 'scifi':
                return self::$movies[array_rand(self::$movies)] . 
                       self::$places[array_rand(self::$places)];
            
            case 'elemental':
                return self::$elements[array_rand(self::$elements)] . 
                       self::$short[array_rand(self::$short)];
            
            default:
                return self::generateRoute(rand(2, 3));
        }
    }
}

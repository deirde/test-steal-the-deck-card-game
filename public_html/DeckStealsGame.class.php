<?php

namespace Deirde\DeckSteals {
    
    /**
     * Opens the session.
     */
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    class Game
    {
        
        /**
         * The session name.
         */
        var $_ = 'deirde_deck_steals_game';
        
        /**
         * Builds a deck of cards.
         * @return array
         */
        public function cards()
        {
            
            $values = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
            $suits  = array('S', 'H', 'D', 'C');
            
            $cards = array();
            foreach ($suits as $suit) {
                foreach ($values as $value) {
                    $cards[] = $value . $suit;
                }
            }
            
            return $cards;
            
        }
        
        /**
         * Shuffles an array of cards.
         * @param array $cards The array of cards to shuffle.
         * @return array
         */
        public function shuffle(array $cards)
        {
            
            $total_cards = count($cards);
            
            foreach ($cards as $index => $card) {
                
                // Pick a random second card.
                $card2_index = mt_rand(1, $total_cards) - 1;
                $card2 = $cards[$card2_index];
                
                // Swap the positions of the two cards.
                $cards[$index] = $card2;
                $cards[$card2_index] = $card;
            }
            
            return $cards;
        }
        
        /**
         * Splits the deck.
         */
        public function split($players = 2)
        {
            
            $cards = $this->shuffle($this->cards());
            
            return array_chunk($cards, intval(ceil(sizeof($cards) / $players)));
            
        }
        
        public function setupTheGame() {
            
            $split = $this->split();
            
            return [
                'cards_on_table' => [],
                'in_progress' => true,
                'player_1' => [
                    'deck' => $split[0],
                ] ,
                'player_2' => [
                    'deck' => $split[1],
                ] ,
                'turn_of' => 'player_' . rand(1, 2)
            ];
            
        }
        
        /**
         * Picks the first card from the deck.
         */
        public function pickTheCard($Game) 
        {
            
            return reset($Game);
            
        }
        
        /**
         *
         */
        public function isTheTurnOf($current_turn)
        {
            
            return (($current_turn == 'player_1') ? 'player_2' : 'player_1');
            
        }
        
        /**
         * Gets the card value.
         */
        public function cardValue($card)
        {
            
            return substr($card, 0, 1);
            
        }
        
    }
    
    $Game = New Game();
    $_ = $Game->_;
    
    if (isset($_GET['submit']) && $_GET['submit'] == 'restart-game') {
        
        unset($_SESSION[$_]);
        
    }
    
    if (isset($_GET['submit']) && $_GET['submit'] == 'start-the-game') {
        
        $_SESSION[$_] = $Game->setupTheGame();
        
    }
    
    if (isset($_GET['submit']) && $_GET['submit'] == 'pick-a-card') {
     
        $_SESSION[$_]['turn_of'] = $Game->isTheTurnOf($_SESSION[$_]['turn_of']);
        
        $picked_card = $Game->pickTheCard($_SESSION[$_][$_SESSION[$_]['turn_of']]['deck']);
        
        $this_card_value = $Game->cardValue($picked_card);
        
        if ($_SESSION[$_]['cards_on_table']) {
            
            $last_card_value = $Game->cardValue(end($_SESSION[$_]['cards_on_table']));
            
            if ($this_card_value == $last_card_value) {
                
                $_SESSION[$_][$_SESSION[$_]['turn_of']]['deck'] =  array_merge(
                    $_SESSION[$_][$_SESSION[$_]['turn_of']]['deck'], 
                    $_SESSION[$_]['cards_on_table']);
                
                unset($_SESSION[$_]['cards_on_table']);
                
            }
            
        }
        
        $_SESSION[$_]['cards_on_table'][] = $picked_card;
        
        array_shift($_SESSION[$_][$_SESSION[$_]['turn_of']]['deck']);
        
    }

}

?>
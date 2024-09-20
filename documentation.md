# SmartArrow API
Version: 2.3.0

## Manager

- Getting a Manager:
    ```PHP
    $manager = Loader::getInstance()->getManager();
    ```

- Register/delete a player so that he can use smart arrows
    ```PHP
    /** 
     * @param pocketmine\player\Player $player
     */
    $manager->registerPlayer($player);
    $manager->removePlayer($player);
    ```

- Check if the player can use smart arrows
    ```PHP
    /** 
     * @param pocketmine\player\Player $player 
     */
    if($manager->hasPlayer($player)){
        // The player uses smart arrows
    }
    ```

- Register and delete smart arrows
    ```PHP
    /** 
     * Register the arrow $arrow as a "smart arrow" that was shot by $owner at the target $target
     * 
     * @param pocketmine\entity\projectile\Arrow $arrow
     * @param pocketmine\player\Player $author
     * @param pocketmine\player\Player $target
     */
    $manager->registerArrow($arrow, $author, $target);

    /** 
     * If the arrow $arrow was a "smart arrow", then it will cease to be one, and the function will return true
     * 
     * @param pocketmine\entity\projectile\Arrow $arrow
     * @return bool
     */
    $manager->destroyArrow($arrow); // return true or false
    ```

## Events
- Standard receipt of an arrow object
    ```PHP
    $event->getArrow(); // return pocketmine\entity\projectile\Arrow
    ```

- Use the `smartarrow\event\CreateSmartArrowEvent` event to track the registration of a smart arrow
    ```PHP
    /**
     * @return pocketmine\player\Player
     */
    $event->getAuthor();
    $event->getTarget();
    ```

- Use the `smartarrow\event\DestroySmartArrowEvent` an event to track the destruction of a smart arrow
- Use the `smartarrow\event\SmartArrowMoveEvent` to track the movement of the arrow at the moment of 1 tick
    ```PHP
    /** 
     * Getting the arrow motion vector
     * @return pocketmine\math\Vector3 
     */
    $event->getMotion();

    /** 
     * getting a separate lead vector for the arrow
     * @return pocketmine\math\Vector3 
     */
    $event->getPreemptive();

    /** 
     * If the target is not in the field of destruction (45°x45°), the function returns true
     * @return bool
     */
    $event->isOverloaded();
    /** 
     * Returns the distance traveled in 1 tick
     * @return float|int
     */
    $event->getTargetSpeed();
    $event->getArrowSpeed();
    ```

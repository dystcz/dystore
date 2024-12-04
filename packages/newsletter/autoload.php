<?php

spl_autoload_register(function ($class) {
    $deprecationMap = [
        'Dystcz\\LunarApiNewsletter' => 'Dystore\\Newsletter',
        'Dystcz\\LunarApiNewsletter\\LunarApiNewsletterServiceProvider' => 'Dystore\\Api\\NewsletterServiceProvider',
        'Dystcz\\LunarApiNewsletter\\LunarApiNewsletter' => 'Dystore\\Newsletter\\Newsletter',
        'Dystcz\\LunarApiNewsletter\\Facades\\LunarApi' => 'Dystore\\Newsletter\\Facades\\Newsletter',
    ];

    foreach ($deprecationMap as $oldNamespace => $newNamespace) {
        if (strpos($class, $oldNamespace) !== 0) {
            continue;
        }

        $newClass = str_replace($oldNamespace, $newNamespace, $class);

        if (! class_exists($newClass)) {
            break;
        }

        $message = __('Class %1$s is <strong>deprecated</strong>! Use %2$s instead.');
        trigger_error(sprintf($message, $class, $newClass), E_USER_DEPRECATED);

        class_alias($newClass, $class);
    }
});

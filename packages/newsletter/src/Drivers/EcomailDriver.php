<?php

namespace Dystore\Newsletter\Drivers;

use Ecomail;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\Newsletter\Drivers\Driver;
use Spatie\Newsletter\Support\Lists;

class EcomailDriver implements Driver
{
    protected Lists $lists;

    protected Ecomail $ecomail;

    /**
     * @param  array<string,string>  $arguments
     */
    public function __construct(array $arguments, Lists $lists)
    {
        $this->lists = $lists;

        $this->ecomail = new Ecomail($arguments['api_key'] ?? '');
    }

    public static function make(array $arguments, Lists $lists): self
    {
        return new self($arguments, $lists);
    }

    /**
     * Get API instance.
     */
    public function getApi(): Ecomail
    {
        return $this->ecomail;
    }

    /**
     * Subscribe a user to a list.
     *
     * @param  array<string,string>  $properties
     * @param  array<string,mixed>  $options
     */
    public function subscribe(string $email, array $properties = [], string $listName = '', array $options = []): array|bool
    {
        $list = $this->lists->findByName($listName);

        return $this->createSubscriber(
            email: $email,
            listId: $list->getId(),
            properties: $properties,
            options: $options
        );
    }

    public function subscribeOrUpdate(
        string $email,
        array $properties = [],
        string $listName = '',
        array $options = []
    ): array|bool {
        $list = $this->lists->findByName($listName);

        // Check if the contact exists
        $contact = $this->getMember($email, $listName);

        // If the contact does not exist, create it
        if (! $contact) {
            return $this->createSubscriber(
                email: $email,
                listId: $list->getId(),
                properties: $properties,
                options: $options
            );
        }

        // If the contact exists, update it
        return $this->updateSubscriber(
            email: $email,
            listId: $list->getId(),
            properties: $properties,
            options: $options
        );
    }

    public function getMember(string $email, string $listName = '', bool $checkList = false): array|false
    {
        $list = $this->lists->findByName($listName);

        try {
            $subscriber = $this->ecomail->getSubscriber(list_id: $list->getId(), email: $email);
        } catch (Exception $e) {
            Log::error('Getting subscriber info in Ecomail failed: '.$e->getMessage().PHP_EOL, $e->getTrace());
        }

        if (! isset($subscriber['subscriber'])) {
            return false;
        }

        // If the contact is not in the list, return false
        if ($checkList && ! in_array((int) $list->getId(), Arr::pluck($subscriber['subscriber']['lists'], 'id'))) {
            return false;
        }

        return $subscriber;
    }

    /**
     * Unsubscribe a contact from a list.
     */
    public function unsubscribe(string $email, string $listName = ''): array|bool
    {
        $list = $this->lists->findByName($listName);

        try {
            $result = $this->ecomail->removeSubscriber(
                list_id: $list->getId(),
                data: ['email' => $email]
            );
        } catch (Exception $e) {
            Log::error('Exception when unsubscribing contact from a list: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return $result;
    }

    public function delete(string $email, string $listName = ''): bool
    {
        try {
            $this->ecomail->deleteSubscriber($email);
        } catch (Exception $e) {
            Log::error('Deleting a subscriber in Ecomail failed: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return true;
    }

    public function hasMember(string $email, string $listName = ''): bool
    {
        return $this->getMember(
            email: $email,
            listName: $listName,
            checkList: false
        ) ? true : false;
    }

    public function isSubscribed(string $email, string $listName = ''): bool
    {
        return $this->getMember(
            email: $email,
            listName: $listName,
            checkList: true
        ) ? true : false;
    }

    /**
     * Create a contact in Brevo.
     *
     * @param  array<string,string>  $properties
     * @param  int[]  $listIds
     * @param  array<string,mixed>  $options
     */
    protected function createSubscriber(
        string $email,
        string $listId,
        array $properties = [],
        array $options = []
    ): array|bool {
        $data = [
            'subscriber_data' => [
                'email' => $email,
            ],
        ];

        if (! empty($properties) && ! array_is_list($properties)) {
            $data['subscriber_data'] = [
                ...$data['subscriber_data'],
                ...$properties,
            ];
        }

        try {
            $result = $this->ecomail->addSubscriber(list_id: $listId, data: $data);
        } catch (Exception $e) {
            Log::error('Creating a subscriber in Ecomail failed: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return $result;
    }

    /**
     * @param  array<string,string>  $properties
     * @param  int[]  $listIds
     * @param  array<string,mixed>  $options
     */
    protected function updateSubscriber(
        string $email,
        string $listId,
        array $properties = [],
        array $options = [],
    ): array|bool {
        $data = [
            'email' => $email,
            'subscriber_data' => [],
        ];

        if (! empty($properties) && ! array_is_list($properties)) {
            $data['subscriber_data'] = $properties;
        }

        try {
            $result = $this->ecomail->updateSubscriber(list_id: $listId, data: $data);
        } catch (Exception $e) {
            Log::error('Updating a subscriber in Ecomail failed: '.$e->getMessage().PHP_EOL, $e->getTrace());

            return false;
        }

        return $result;
    }
}

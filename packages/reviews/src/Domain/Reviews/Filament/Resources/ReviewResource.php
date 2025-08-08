<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Resources;

use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource\Pages\CreateReview;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource\Pages\EditReview;
use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource\Pages\ListReviews;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return __('dystore-reviews::reviews.navigation.group');
    }

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return __('dystore-reviews::reviews.model.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('dystore-reviews::reviews.navigation.label');
    }

    public static function getPluralLabel(): string
    {
        return __('dystore-reviews::reviews.model.plural_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dystore-reviews::reviews.model.plural_label');
    }

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('dystore-reviews::reviews.fields.title'))
                    ->maxLength(255)
                    ->nullable(),

                MorphToSelect::make('purchasable')
                    ->label(__('dystore-reviews::reviews.fields.purchasable'))
                    ->types([
                        Type::make(ProductVariant::class)
                            ->titleAttribute('sku')
                            ->getOptionLabelFromRecordUsing(
                                fn (ProductVariant $record): string => $record->name
                            ),
                    ])
                    ->searchable()
                    ->preload(),

                Select::make('user_id')
                    ->label(__('dystore-reviews::reviews.fields.user'))
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name ?: $record->email
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('rating')
                    ->label(__('dystore-reviews::reviews.fields.rating'))
                    ->options([
                        1 => __('dystore-reviews::reviews.rating.1'),
                        2 => __('dystore-reviews::reviews.rating.2'),
                        3 => __('dystore-reviews::reviews.rating.3'),
                        4 => __('dystore-reviews::reviews.rating.4'),
                        5 => __('dystore-reviews::reviews.rating.5'),
                    ])
                    ->required()
                    ->native(false),

                Textarea::make('comment')
                    ->label(__('dystore-reviews::reviews.fields.comment'))
                    ->rows(4)
                    ->maxLength(65535)
                    ->nullable()
                    ->columnSpanFull(),

                Select::make('status')
                    ->label(__('dystore-reviews::reviews.fields.status'))
                    ->options(PublishedStatus::class)
                    ->required()
                    ->native(false),

                DateTimePicker::make('published_at')
                    ->label(__('dystore-reviews::reviews.fields.published_at'))
                    ->nullable()
                    ->seconds(false),

                KeyValue::make('meta')
                    ->label(__('dystore-reviews::reviews.fields.meta_data'))
                    ->keyLabel(__('dystore-reviews::reviews.fields.meta_key'))
                    ->valueLabel(__('dystore-reviews::reviews.fields.meta_value'))
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('dystore-reviews::reviews.fields.title'))
                    ->sortable()
                    ->searchable()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label(__('dystore-reviews::reviews.fields.user'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('purchasable_type')
                    ->label(__('dystore-reviews::reviews.fields.type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('rating')
                    ->label(__('dystore-reviews::reviews.fields.rating'))
                    ->icon(fn (int $state): string => 'heroicon-o-star')
                    ->color(fn (int $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->tooltip(fn (int $state): string => "{$state} star".($state !== 1 ? 's' : ''))
                    ->sortable(),

                TextColumn::make('comment')
                    ->label(__('dystore-reviews::reviews.fields.comment'))
                    ->searchable()
                    ->limit(100)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 100) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label(__('dystore-reviews::reviews.fields.status'))
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('published_at')
                    ->label(__('dystore-reviews::reviews.fields.published'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('dystore-reviews::reviews.fields.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('dystore-reviews::reviews.fields.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label(__('dystore-reviews::reviews.filters.rating'))
                    ->options([
                        1 => __('dystore-reviews::reviews.rating.1'),
                        2 => __('dystore-reviews::reviews.rating.2'),
                        3 => __('dystore-reviews::reviews.rating.3'),
                        4 => __('dystore-reviews::reviews.rating.4'),
                        5 => __('dystore-reviews::reviews.rating.5'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('dystore-reviews::reviews.filters.status'))
                    ->multiple(true)
                    ->options(PublishedStatus::class),

                SelectFilter::make('user')
                    ->label(__('dystore-reviews::reviews.filters.user'))
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name ?: $record->email
                    )
                    ->searchable()
                    ->preload(),

                TrashedFilter::make()
                    ->label(__('dystore-reviews::reviews.filters.deleted')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}

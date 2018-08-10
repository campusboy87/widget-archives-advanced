<?php
/**
 * Plugin Name: Widget Archives Advanced
 * Plugin URI: https://github.com/campusboy87/widget-archives-advanced
 * Description: Добавляет улучшенный виджет архивов.
 * Version: 1.0
 * Author: campusboy
 * Author URI: https://wp-plus.ru/
 * License: MIT
 */

defined( 'ABSPATH' ) or die();

/**
 * Добавляет виджет улучшенных архивов на панель доступных виджетов.
 *
 * @return void
 */
function register_widget_archives_advanced() {

    class WP_Widget_Archives_Advanced extends WP_Widget_Archives {

        /**
         * @var array Дефолтные параметры.
         */
        public $default = [ 'type' => 'monthly', 'limit' => '', 'post_type' => 'post', ];

        /**
         * WP_Widget_Archives_Advanced constructor.
         */
        public function __construct() {
            $widget_ops = [
                'description' => 'Улучшенный виджет с архивами.',
            ];

            WP_Widget::__construct( 'archives_advanced', 'Улучшенные архивы', $widget_ops );
        }

        /**
         * HTML форма настроек виджета в Админ-панели.
         *
         * @param array $instance
         *
         * @return void
         */
        public function form( $instance ) {
            parent::form( $instance );

            $instance = wp_parse_args( (array) $instance, $this->default );

            $options = [
                'monthly'    => 'По месяцам',
                'yearly'     => 'По годам',
                'daily'      => 'По дням',
                'weekly'     => 'По неделям',
                'postbypost' => 'По постам (сорт. по дате)',
                'alpha'      => 'По постам (сорт. по заголовку)',
            ];
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'type' ); ?>">Тип архива</label>
                <select name="<?php echo $this->get_field_name( 'type' ); ?>"
                        id="<?php echo $this->get_field_id( 'type' ); ?>"
                        class="widefat"
                >
                    <?php foreach ( $options as $value => $text ): ?>
                        <option value="<?php echo esc_attr( $value ); ?>"<?php selected( $instance['type'], $value ); ?>>
                            <?php echo esc_html( $text ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'limit' ); ?>">Количество ссылок на архивы</label>
                <input type="number"
                       name="<?php echo $this->get_field_name( 'limit' ); ?>"
                       id="<?php echo $this->get_field_id( 'limit' ); ?>"
                       class="widefat"
                       value="<?php echo isset( $instance['limit'] ) ? esc_attr( $instance['limit'] ) : ''; ?>"
                >
                <small>Оставьте поле пустым, чтобы отобразить все ссылки</small>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'post_type' ); ?>">Тип записи</label>
                <select name="<?php echo $this->get_field_name( 'post_type' ); ?>"
                        id="<?php echo $this->get_field_id( 'post_type' ); ?>"
                        class="widefat"
                >
                    <?php
                    $post_types = get_post_types( [
                        'public'            => true,
                        'show_in_nav_menus' => true,
                    ], 'objects' );

                    foreach ( $post_types as $post_type ): ?>
                        <option
                                value="<?php echo esc_attr( $post_type->name ); ?>"
                            <?php selected( $instance['post_type'], $post_type->name ); ?>
                        >
                            <?php echo esc_html( $post_type->label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <?php
        }

        /**
         * Сохраняет настроеки виджета.
         *
         * @param array $new_instance
         * @param array $old_instance
         *
         * @return array
         */
        public function update( $new_instance, $old_instance ) {
            $new_instance = wp_parse_args( (array) $new_instance, $this->default );

            $instance              = parent::update( $new_instance, $old_instance );
            $instance['type']      = sanitize_key( $new_instance['type'] );
            $instance['limit']     = $new_instance['limit'] > 0 ? (int) $new_instance['limit'] : '';
            $instance['post_type'] = sanitize_key( $new_instance['post_type'] );

            return $instance;
        }

        /**
         * Выводит виджет в лицевой части сайта.
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {
            add_filter( 'widget_archives_dropdown_args', [ $this, 'add_args' ], 10, 2 );
            add_filter( 'widget_archives_args', [ $this, 'add_args' ], 10, 2 );

            parent::widget( $args, $instance );

            remove_filter( 'widget_archives_dropdown_args', [ $this, 'add_args' ] );
            remove_filter( 'widget_archives_args', [ $this, 'add_args' ] );
        }

        /**
         * Изменяет с помощью хук-фильтров параметры, передаваемые в функцию wp_get_archives().
         *
         * @see wp_get_archives()
         *
         * @param $args
         * @param $instance
         *
         * @return array
         */
        public function add_args( $args, $instance ) {
            return wp_parse_args( $instance, $args );
        }
    }

    register_widget( 'WP_Widget_Archives_Advanced' );
}

add_action( 'widgets_init', 'register_widget_archives_advanced' );

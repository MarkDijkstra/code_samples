<?php
namespace frontend\components;
use common\widgets\ClientScript;
use yii\web\View;

class NavigationBlock
{
    public $limit;
    public $array;
    public $currentPage;
    public $totalPages;
    public $totalItems;

    /**
     * ArrayToPager constructor.
     * @param bool $array
     * @param int $limit
     */
    public function __construct($array = false , $limit = 10)
    {
        $this->limit       = self::setPerPage($limit);
        $this->array       = is_array($array) ? $array : [];
        $this->totalItems  = self::totalItems();
        $this->totalPages  = self::totalPages();
        $this->currentPage = self::currentPage();
    }

    /**
     * @return int
     */
    public function totalItems()
    {
        return count ($this->array);
    }

    /**
     * @return array|bool
     */
    public function arrayPerPage()
    {
        $offset = self::offset();

        $total      = count($array);
        $totalPages = ceil($total / $limit);
        $page       = ! empty($_GET['page']) ? (int) $_GET['page'] : 1;
        $page       = max($page, 1);
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $limit;
        $offset     = $offset < 0 ? 0 : $offset;
        $output     = [];
        
        foreach ($array as $item) {
           $output[] = array_slice($item, $offset, $limit);
        }
        
        return array_slice ($this->array, $offset, $this->limit);
    }

    /**
     * @param $array
     * @param $limit
     * @return float|int
     */
    private function offset()
    {
        $page       = !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
        $page       = $page > $this->totalPages ? $this->totalPages : $page;
        $page       = max( $page, 1);
        $page       = min( $page,  $this->totalPages);
        $offset     = ($page - 1) * $this->limit;
        $offset     = $offset < 0 ? 0 :  $offset;

        return $offset;
    }

    /**
     * @return int
     */
    public function currentPage()
    {
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            return $_GET['page'] <= $this->totalPages ? $_GET['page'] : $this->totalPages ;
        } else {
            return 1;
        }
    }

    /**
     * @param $limit
     * @return int
     */
    public static function setPerPage($limit)
    {
        return is_integer($limit) ? $limit : 10;
    }

    /**
     * @return int
     */
    public function perPage()
    {
        return $this->limit;
    }

    /**
     * @param $array
     * @param $limit
     * @return float
     */
    private function totalPages()
    {
        return ceil($this->totalItems / $this->limit);
    }

    /**
     * void
     */
    public function showPager()
    {
        $prevPage = $this->currentPage != 1 ? ($this->currentPage - 1) : 1;
        $nextPage = $this->currentPage <= $this->totalPages ? ($this->currentPage + 1) : $this->totalPages;
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $parseUrl = parse_url($link, PHP_URL_QUERY);
        parse_str($parseUrl, $arrayParams);

        // remove page=x param from array
        if (isset($arrayParams['page'])) {
            unset($arrayParams['page']);
        }

        $strippedUrl = explode('?', $link);
        $strippedUrl = $strippedUrl[0];

        // get all parameters from url(if present)
        if (strpos($link, '?') !== false && count($arrayParams) > 0) {
            $params = implode('&', array_map(
                function ($v, $k) {
                    return sprintf("%s=%s", $k, $v);
                }, $arrayParams, array_keys($arrayParams)
            ));
            $params = '?' . $params . '&';
        } else {
            $params = '?';
        }

        $url = $strippedUrl . $params . 'page=';

        if ($this->totalItems > $this->limit) { 
            ?>
            <div class="x3_pager">
                <div class="x3_pager__ctrls x3_pager--prev">
                    <?php if ($this->currentPage != 1) { ?>
                        <a href="<?= $url . $prevPage ?>">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    <?php } else { ?>
                        <p>
                            <i class="fas fa-angle-left"></i>
                        </p>
                    <?php } ?>
                </div>
                <div class="x3_pager__ctrls x3_pager--select fakeselect">
                    <select data-trigger="pager">
                        <?php for ($i = 1; $i <= $this->totalPages; $i++) {
                            $selected = '';

                            if ($this->currentPage == $i) {
                                $selected = 'selected';
                            }

                            echo '<option value="' . $url . $i . '" ' . $selected . ' >' . $i . '</option>';

                        } ?>
                    </select>
                    <span><i class="fas fa-sort-down"></i></span>
                </div>
                <div class="x3_pager__ctrls x3_pager--next">
                    <?php if ($nextPage <= $this->totalPages) { ?>
                        <a href="<?= $url . $nextPage ?>">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    <?php } else { ?>
                        <p>
                            <i class="fas fa-angle-right"></i>
                        </p>
                    <?php } ?>
                </div>
            </div>
            <?php
                $cs = new ClientScript;
                $cs->beginJs(View::POS_END, 'array-pager');
            ?>
            <script type='text/javascript'>
                $(document).ready(function () {
                    $('body').on('change', '[data-trigger=pager]', function () {
                        window.location = $(this).val();
                    });
                });
            </script>
            <?php
                $cs->endJs();
        }
    }
}

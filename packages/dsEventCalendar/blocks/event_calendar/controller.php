<?php 
namespace Concrete\Package\Dseventcalendar\Block\EventCalendar;
use Concrete\Core\Block\BlockController;
use Loader;
use Config;
use DsEventCalendar;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends BlockController
{

    protected $btName = "Event Calendar";
    protected $btDescription = "";
    public $btTable = 'btEventCalendar';
    public $btInterfaceWidth = '400';
    public $btInterfaceHeight = '200';
    protected $btWrapperClass = 'ccm-ui';
    private  $lang_list = array("ar-ma","ar-sa","ar","bg","ca","cs","da","de-at","de","el","en-au","en-ca","en-gb","es","fa","fi","fr-ca","fr","he","hi","hr","hu","id","is","it","ja","ko","lt","lv","nl","pl","pt-br","pt","ro","ru","sk","sl","sr-cyrl","sr","sv","th","tr","uk","vi","zh-cn","zh-tw");

    public function getBlockTypeDescription()
    {
        return $this->btDescription;
    }

    public function getBlockTypeName()
    {
        return t("Event Calendar");
    }

    public function __construct($b = null)
    {
        parent::__construct($b);
    }

    public function view()
    {

        $db = Loader::db();
        $calendar = $db->GetAll("SELECT * FROM dsEventCalendar WHERE calendarID=" . $this->calendarID);
        $this->set('calendar', $calendar);

        $dsEventCalendar = new \dsEventCalendar\dsEventCalendar();

        $json_events = $dsEventCalendar->getEventsFromCalendar($this->calendarID,$this->typeID);

        $this->set('events', $json_events);
        $this->set('settings',$dsEventCalendar->settingsProvider());
        $this->set('typeID',$this->typeID);

        if(method_exists($this->getBlockObject(),'getProxyBlock'))
        {
            $this->set(
                'blockIdentifier',
                $this->getBlockObject()->getProxyBlock()
                    ? $this->getBlockObject()->getProxyBlock()->getInstance()->getIdentifier()
                    : $this->getIdentifier()
            );
        }
        else
        {
            $this->set('blockIdentifier',rand(12,512));
        }

    }

    function save($data)
    {
        $args['calendarID'] = isset($data['calendarID']) ? intval($data['calendarID']) : 0;
        $args['typeID'] = isset($data['typeID']) ? intval($data['typeID']) : 0;
        $args['lang'] = isset($data['lang']) ? $data['lang'] : 'en-gb';
        parent::save($args);
    }

    function add()
    {
        $db = Loader::db();
        $calendars = $db->GetAll("SELECT * FROM dsEventCalendar");
        $this->set('calendars', $calendars);

        $this->set('langs', $this->lang_list);

        $dsEventCalendar = new \dsEventCalendar\dsEventCalendar();
        $types = $dsEventCalendar->getEventTypesForBlock();
        $this->set('types',$types);
        $this->set('types',$types);
    }

    function edit()
    {
        $db = Loader::db();
        $calendars = $db->GetAll("SELECT * FROM dsEventCalendar");
        $this->set('calendars', $calendars);
        $this->set('calendarID', $this->calendarID);

        $this->set('langs', $this->lang_list);
        $this->set('lang',$this->lang);

        $dsEventCalendar = new \dsEventCalendar\dsEventCalendar();
        $types = $dsEventCalendar->getEventTypesForBlock();
        $this->set('types',$types);
        $this->set('typeID',$this->typeID);
    }
}

?>

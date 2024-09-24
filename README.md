# PHP-GA-API
Fetch google Analytics Data


    $data = (new googleAnalyticsDataApi([
         'path'          =>  public_path('ga-embed-api-436503-c71aff162a41.json'),
         'property_id'   =>  '459535268',
         'start_date'    =>  $this->getParamValue('start_date'),
         'end_date'      =>  $this->getParamValue('end_date')
    ]))->fetch();

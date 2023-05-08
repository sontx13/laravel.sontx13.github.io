<?php

namespace App\Constants;

class Constants
{
    //Ref điểm danh tenantid/diemdanhs/buoihopid/userid
    const REF_DIEMDANH = '%d/diemdanhs/%d/%d';
    //Ref biểu quyết đang diễn ra tenantid/bieuquyets/kyhopid/dangdienra
    const REF_BIEUQUYETDANGDIENRA = '%d/bieuquyets/%d/dangdienra';
    //Ref điểm danh tenantid/bieuquyets/kyhopid/bieuquyetid/userid
    const REF_KQBIEUQUYET = '%d/bieuquyets/%d/%d/%d';
    //Ref phiên chất vấn đang diễn ra tenantid/chatvans/kyhopid/dangdienra
    const REF_PHIENCHATVANDANGDIENRA = '%d/chatvans/%d/dangdienra';
    //Ref đại biểu chất vấn tenantid/chatvans/kyhopid/phienchatvanid/userid
    const REF_DAIBIEUCHATVAN = '%d/chatvans/%d/%d/%d';
}
